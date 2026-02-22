<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [];

        for ($index = 1; $index <= 10; $index++) {
            $companies[] = [
                'name' => "Company {$index}",
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('companies')->insert($companies);
    }
}
