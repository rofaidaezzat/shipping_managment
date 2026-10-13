<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Port;
use Illuminate\Database\Seeder;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all()->keyBy('code');

        $ports = [
            // Maersk ports (5 ports)
            ['company_id' => $companies['MSK']->id, 'name' => 'Alexandria Port',     'code' => 'ALX', 'country' => 'Egypt',                'is_active' => true],
            ['company_id' => $companies['MSK']->id, 'name' => 'Port Said West',      'code' => 'PSW', 'country' => 'Egypt',                'is_active' => true],
            ['company_id' => $companies['MSK']->id, 'name' => 'Damietta Port',       'code' => 'DAM', 'country' => 'Egypt',                'is_active' => true],
            ['company_id' => $companies['MSK']->id, 'name' => 'Sokhna Port',         'code' => 'SKH', 'country' => 'Egypt',                'is_active' => true],
            ['company_id' => $companies['MSK']->id, 'name' => 'Dekheila Port',       'code' => 'DKH', 'country' => 'Egypt',                'is_active' => false],

            // MSC ports (5 ports) - note ALX code can be reused because composite unique is (company_id, code)
            ['company_id' => $companies['MSC']->id, 'name' => 'Alexandria Hub',      'code' => 'ALX', 'country' => 'Egypt',                'is_active' => true],
            ['company_id' => $companies['MSC']->id, 'name' => 'Port Said East',      'code' => 'PSE', 'country' => 'Egypt',                'is_active' => true],
            ['company_id' => $companies['MSC']->id, 'name' => 'Rotterdam Gateway',   'code' => 'RTM', 'country' => 'Netherlands',          'is_active' => true],
            ['company_id' => $companies['MSC']->id, 'name' => 'Antwerp Terminal',    'code' => 'ANR', 'country' => 'Belgium',              'is_active' => true],
            ['company_id' => $companies['MSC']->id, 'name' => 'Valencia Port',       'code' => 'VLC', 'country' => 'Spain',                'is_active' => false],

            // CMA CGM ports (5 ports)
            ['company_id' => $companies['CMA']->id, 'name' => 'Marseille Fos',       'code' => 'MRS', 'country' => 'France',               'is_active' => false],
            ['company_id' => $companies['CMA']->id, 'name' => 'Le Havre Terminal',   'code' => 'LEH', 'country' => 'France',               'is_active' => false],
            ['company_id' => $companies['CMA']->id, 'name' => 'Beirut Port',         'code' => 'BEY', 'country' => 'Lebanon',              'is_active' => true],
            ['company_id' => $companies['CMA']->id, 'name' => 'Tangier Med',         'code' => 'TNG', 'country' => 'Morocco',              'is_active' => true],
            ['company_id' => $companies['CMA']->id, 'name' => 'Jebel Ali Port',      'code' => 'DXB', 'country' => 'United Arab Emirates', 'is_active' => true],
        ];

        foreach ($ports as $port) {
            Port::create($port);
        }
    }
}
