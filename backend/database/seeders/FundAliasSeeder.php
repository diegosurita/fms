<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FundAliasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fundIds = DB::table('funds')->pluck('id')->all();

        if (empty($fundIds)) {
            return;
        }

        $aliases = [];

        foreach ($fundIds as $index => $fundId) {
            $aliasNumber = $index + 1;

            $aliases[] = [
                'alias' => "fund-alias-{$aliasNumber}",
                'fund' => $fundId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('fund_aliases')->insert($aliases);
    }
}
