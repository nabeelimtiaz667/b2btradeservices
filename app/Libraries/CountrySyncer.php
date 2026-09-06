<?php

namespace App\Libraries;

use Config\Database;

/**
 * Core logic behind `php spark countries:sync` (app/Commands/CountriesSync.php),
 * extracted here so the admin "Countries" page (AdminSettings::syncCountriesNow())
 * can trigger the exact same sync over HTTP without duplicating it. See that
 * command's original docblock for the full why: mledoze/countries dataset
 * (free, keyless) + flagcdn.com, DB `countries` table read once just to
 * preserve existing ids, CountryModel never touches the DB itself.
 */
class CountrySyncer
{
    private const SOURCE_URLS = [
        'https://raw.githubusercontent.com/mledoze/countries/master/countries.json',
        'https://cdn.jsdelivr.net/gh/mledoze/countries@master/countries.json',
    ];
    private const OUTPUT_PATH = APPPATH . 'Data/countries.php';

    /**
     * @return array{success: bool, message: string, total: int, new: int, log: string[]}
     */
    public function sync(): array
    {
        $log = [];
        $countries = null;
        $client = service('curlrequest', ['timeout' => 30]);

        foreach (self::SOURCE_URLS as $url) {
            $log[] = 'Fetching country list from ' . parse_url($url, PHP_URL_HOST) . '...';

            try {
                $response = $client->get($url);
            } catch (\Throwable $e) {
                $log[] = '  failed: ' . $e->getMessage();
                continue;
            }

            if ($response->getStatusCode() !== 200) {
                $log[] = '  failed: HTTP ' . $response->getStatusCode();
                continue;
            }

            $decoded = json_decode($response->getBody(), true);
            if (is_array($decoded) && $decoded !== []) {
                $countries = $decoded;
                break;
            }
            $log[] = '  failed: response was not a valid JSON array';
        }

        if ($countries === null) {
            return ['success' => false, 'message' => 'All sources failed. Existing country data left untouched.', 'total' => 0, 'new' => 0, 'log' => $log];
        }

        // Existing DB rows are the source of truth for id stability -- any
        // code already in production keeps its exact id, so
        // users/buyer_inquiries/contact_submissions.country_id values never
        // need to change. New codes get new ids appended after the current max.
        [$codeToId, $nextId] = $this->loadExistingIds($log);

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
            // Sanity floor -- the real dataset has ~250 countries/territories.
            // A short list means something went wrong upstream; don't clobber
            // a good file with a truncated one.
            return ['success' => false, 'message' => 'Only ' . count($rows) . ' countries parsed (expected 190+). Aborted without writing.', 'total' => count($rows), 'new' => $newCount, 'log' => $log];
        }

        ksort($rows);
        $this->writeAtomic($rows);

        return [
            'success' => true,
            'message' => 'Synced ' . count($rows) . ' countries (' . $newCount . ' new).',
            'total' => count($rows),
            'new' => $newCount,
            'log' => $log,
        ];
    }

    /**
     * @return array{0: array<string,int>, 1: int} [codeToId map, next free id]
     */
    private function loadExistingIds(array &$log): array
    {
        try {
            $db = Database::connect();
            if (!$db->tableExists('countries')) {
                return [[], 1];
            }
            $existing = $db->table('countries')->select('id, code')->get()->getResultArray();
        } catch (\Throwable $e) {
            // No DB reachable -- fall back to a fresh id sequence rather than
            // failing the sync.
            $log[] = 'Could not read existing countries table (' . $e->getMessage() . ') -- assigning fresh ids.';
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
            . "// Auto-generated by countries:sync on " . date('Y-m-d H:i:s') . " -- do not edit by hand.\n"
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
