<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\CountrySyncer;

/**
 * `php spark countries:sync` -- refreshes app/Data/countries.php. Thin CLI
 * wrapper around CountrySyncer, which also backs the "Update Now" button on
 * the admin Countries page (AdminSettings::syncCountriesNow()) so both entry
 * points run the exact same logic.
 */
class CountriesSync extends BaseCommand
{
    protected $group       = 'demo';
    protected $name        = 'countries:sync';
    protected $description = 'Refresh app/Data/countries.php from the mledoze/countries dataset';

    public function run(array $params)
    {
        $result = (new CountrySyncer())->sync();

        foreach ($result['log'] as $line) {
            CLI::write($line);
        }

        if (!$result['success']) {
            CLI::error($result['message']);
            return;
        }

        CLI::write($result['message']);
    }
}
