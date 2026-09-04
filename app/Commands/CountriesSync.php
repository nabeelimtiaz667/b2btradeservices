<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * `php spark countries:sync` -- refreshes app/Data/countries.php from the
 * mledoze/countries dataset (github.com/mledoze/countries), a free,
 * keyless, community-maintained JSON snapshot with the same field shape
 * REST Countries used to have. restcountries.com's own free API was
 * discontinued (as of this writing it requires an account + API key), so
 * this reads a static file instead -- no auth, no rate limit, no account.
 *
 * This is the ONE place that talks to the DB `countries` table at all
 * (purely to preserve existing ids for country_id foreign keys already
 * sitting in users/buyer_inquiries/contact_submissions -- see the plan doc).
 * CountryModel itself never touches the DB; it only reads the generated
 * file this command writes.
 */
class CountriesSync extends BaseCommand
{
    protected $group       = 'demo';
    protected $name        = 'countries:sync';
    protected $description = 'Refresh app/Data/countries.php from the mledoze/countries dataset';

    // Primary: GitHub raw (canonical source, updated whenever the repo is).
    // Fallback: jsDelivr's CDN mirror of the same file, in case GitHub raw
    // is ever unreachable from wherever this runs.
    private const SOURCE_URLS = [
        'https://raw.githubusercontent.com/mledoze/countries/master/countries.json',
        'https://cdn.jsdelivr.net/gh/mledoze/countries@master/countries.json',
    ];
    private const OUTPUT_PATH = APPPATH . 'Data/countries.php';

    public function run(array $params)
    {
        $countries = null;
        $client = service('curlrequest', ['timeout' => 30]);

        foreach (self::SOURCE_URLS as $url) {
            CLI::write('Fetching country list from ' . parse_url($url, PHP_URL_HOST) . '...');

            try {
                $response = $client->get($url);
            } catch (\Throwable $e) {
                CLI::write('  failed: ' . $e->getMessage());
                continue;
            }

            if ($response->getStatusCode() !== 200) {
                CLI::write('  failed: HTTP ' . $response->getStatusCode());
                continue;
            }

            $decoded = json_decode($response->getBody(), true);
            if (is_array($decoded) && $decoded !== []) {
                $countries = $decoded;
                break;
            }
            CLI::write('  failed: response was not a valid JSON array');
        }

        if ($countries === null) {
            CLI::error('All sources failed. Existing app/Data/countries.php left untouched.');
            return;
        }

        // Existing DB rows are the source of truth for id stability --
        // any code already in production keeps its exact id, so
        // users/buyer_inquiries/contact_submissions.country_id values never
        // need to change. New codes get new ids appended after the current max.
        [$codeToId, $nextId] = $this->loadExistingIds();

        $rows = [];
        $newCount = 0;

        foreach ($countries as $entry) {
            $code = strtoupper((string) ($entry['cca2'] ?? ''));
            $name = (string) ($entry['name']['common'] ?? '');

            if ($code === '' || $name === '') {
                continue; // malformed entry, skip rather than write a broken row
            }

            // idd.suffixes has exactly one entry for the normal case (root +
            // that suffix = the country's dial code, e.g. GB: "+4"+"4" =
            // "+44"). Countries sharing a calling code across many
            // territories (NANP: US/CA/... all root "+1") instead list every
            // member territory's area-code-style suffix here (US alone has
            // 300+), so root+suffixes[0] would wrongly produce something
            // like "+1201". In that case (0 or 2+ suffixes) the country-level
            // dial code is just the root by itself.
            $idd = $entry['idd'] ?? [];
            $root = (string) ($idd['root'] ?? '');
            $suffixes = $idd['suffixes'] ?? [];
            $phoneCode = $root === '' ? '' : (count($suffixes) === 1 ? $root . (string) $suffixes[0] : $root);

            if (isset($codeToId[$code])) {
                $id = $codeToId[$code];
            } else {
                $id = $nextId++;
                $newCount++;
            }

            $rows[$id] = [
                'id' => $id,
                'name' => $name,
                'code' => $code,
                'phone_code' => $phoneCode,
                'flag' => 'https://flagcdn.com/' . strtolower($code) . '.svg',
                'region' => (string) ($entry['region'] ?? ''),
                'status' => 'active',
            ];
        }

        if (count($rows) < 190) {
            // Sanity floor -- the real API returns ~250 countries/territories.
            // A short list means something went wrong upstream; don't clobber
            // a good file with a truncated one.
            CLI::error('Only ' . count($rows) . ' countries parsed (expected ~190+). Aborting without writing.');
            return;
        }

        ksort($rows);
        $this->writeAtomic($rows);

        CLI::write('Synced ' . count($rows) . ' countries (' . $newCount . ' new) to app/Data/countries.php');
    }

    /**
     * @return array{0: array<string,int>, 1: int} [codeToId map, next free id]
     */
    private function loadExistingIds(): array
    {
        try {
            $db = Database::connect();
            if (!$db->tableExists('countries')) {
                return [[], 1];
            }
            $existing = $db->table('countries')->select('id, code')->get()->getResultArray();
        } catch (\Throwable $e) {
            // No DB reachable (e.g. this ever runs somewhere without one) --
            // fall back to a fresh id sequence rather than failing the sync.
            CLI::write('Could not read existing countries table (' . $e->getMessage() . ') -- assigning fresh ids.');
            return [[], 1];
        }

        $codeToId = [];
        $maxId = 0;
        foreach ($existing as $row) {
            $code = strtoupper($row['code']);
            $id = (int) $row['id'];
            $codeToId[$code] = $id;
            $maxId = max($maxId, $id);
        }

        return [$codeToId, $maxId + 1];
    }

    private function writeAtomic(array $rows): void
    {
        $export = var_export($rows, true);

        $php = "<?php\n\n"
            . "// Auto-generated by `php spark countries:sync` on " . date('Y-m-d H:i:s') . " -- do not edit by hand.\n"
            . "// Source: mledoze/countries (https://github.com/mledoze/countries) + flagcdn.com.\n"
            . "// Keyed by id so country_id foreign keys in users/buyer_inquiries/contact_submissions\n"
            . "// keep resolving correctly; ids for existing codes are preserved across syncs.\n"
            . "return " . $export . ";\n";

        $dir = dirname(self::OUTPUT_PATH);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $tmpPath = self::OUTPUT_PATH . '.tmp';
        file_put_contents($tmpPath, $php, LOCK_EX);
        rename($tmpPath, self::OUTPUT_PATH); // atomic on both POSIX and Windows NTFS
    }
}
