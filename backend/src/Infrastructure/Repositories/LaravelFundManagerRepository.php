<?php

namespace FMS\Infrastructure\Repositories;

use FMS\Core\Contracts\FundManagerRepository;
use FMS\Core\DataTransferObjects\SaveFundManagerDTO;
use FMS\Core\Entities\FundManagerEntity;
use FMS\Infrastructure\Adapter\LaravelFundManagerEntityAdapter;
use Illuminate\Support\Facades\DB;

class LaravelFundManagerRepository extends LaravelRepository implements FundManagerRepository
{
    public function list(): array
    {
        $query = DB::table('fund_managers')
            ->whereNull('deleted_at')
            ->select([
                'id',
                'name',
                'created_at',
                'updated_at',
            ]);

        return $query
            ->get()
            ->map(fn (object $row) => LaravelFundManagerEntityAdapter::fromDB((array) $row))
            ->all();
    }

    public function create(SaveFundManagerDTO $saveFundManagerDTO): FundManagerEntity
    {
        $fundManagerId = DB::table('fund_managers')->insertGetId([
            'name' => $saveFundManagerDTO->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /** @var object $fundManager */
        $fundManager = DB::table('fund_managers')
            ->select([
                'id',
                'name',
                'created_at',
                'updated_at',
            ])
            ->where('id', $fundManagerId)
            ->first();

        return LaravelFundManagerEntityAdapter::fromDB((array) $fundManager);
    }
}
