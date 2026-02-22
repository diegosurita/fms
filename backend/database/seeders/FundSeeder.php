<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $managerIds = DB::table('fund_managers')->pluck('id')->all();

        if (empty($managerIds)) {
            return;
        }

        $funds = [];

        for ($index = 1; $index <= 10; $index++) {
            $managerId = $managerIds[($index - 1) % count($managerIds)];

            $funds[] = [
                'name' => "Fund {$index}",
                'start_year' => (int) now()->subYears($index)->format('Y'),
                'manager_id' => $managerId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('funds')->insert($funds);
    }
}
