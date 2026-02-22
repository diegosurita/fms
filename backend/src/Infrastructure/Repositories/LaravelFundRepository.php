<?php

namespace FMS\Infrastructure\Repositories;

use FMS\Core\Contracts\FundRepository;
use FMS\Infrastructure\Adapter\LaravelFundRepositoryAdapter;
use Illuminate\Support\Facades\DB;

class LaravelFundRepository implements FundRepository
{
    public function list(): array
    {
        return DB::table('funds')
            ->select(['id', 'name', 'start_year', 'manager', 'created_at', 'updated_at'])
            ->get()
            ->map(fn (object $row) => LaravelFundRepositoryAdapter::fromDB((array) $row))
            ->all();
    }
}
