<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FundManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $managers = [];

        for ($index = 1; $index <= 5; $index++) {
            $managers[] = [
                'name' => "Fund Manager {$index}",
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('fund_managers')->insert($managers);
    }
}
