<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            ['name' => 'Maersk',   'code' => 'MSK', 'is_active' => true],
            ['name' => 'MSC',      'code' => 'MSC', 'is_active' => true],
            ['name' => 'CMA CGM', 'code' => 'CMA', 'is_active' => false],
        ];

        foreach ($companies as $data) {
            Company::create($data);
        }
    }
}
