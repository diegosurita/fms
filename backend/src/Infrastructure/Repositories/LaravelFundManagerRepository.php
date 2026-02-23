<?php

namespace FMS\Infrastructure\Repositories;

use FMS\Core\Contracts\FundManagerRepository;
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
}
