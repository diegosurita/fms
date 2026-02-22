<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyFundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyIds = DB::table('companies')->pluck('id')->all();
        $fundIds = DB::table('funds')->pluck('id')->all();

        if (empty($companyIds) || empty($fundIds)) {
            return;
        }

        $companiesFunds = [];

        foreach ($fundIds as $index => $fundId) {
            $primaryCompanyId = $companyIds[$index % count($companyIds)];

            $companiesFunds[] = [
                'company' => $primaryCompanyId,
                'fund' => $fundId,
            ];

            if (count($companyIds) > 1) {
                $secondaryCompanyId = $companyIds[($index + 1) % count($companyIds)];

                if ($secondaryCompanyId !== $primaryCompanyId) {
                    $companiesFunds[] = [
                        'company' => $secondaryCompanyId,
                        'fund' => $fundId,
                    ];
                }
            }
        }

        DB::table('companies_funds')->insert($companiesFunds);
    }
}
