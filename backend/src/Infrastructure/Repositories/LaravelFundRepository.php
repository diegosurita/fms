<?php

namespace FMS\Infrastructure\Repositories;

use FMS\Core\Contracts\FundRepository;
use FMS\Infrastructure\Adapter\LaravelFundRepositoryAdapter;
use Illuminate\Support\Facades\DB;

class LaravelFundRepository implements FundRepository
{
    public function list(?string $filter = null): array
    {
        $query = DB::table('funds')
            ->join('fund_managers', 'fund_managers.id', '=', 'funds.manager_id')
            ->leftJoin('companies_funds', 'companies_funds.fund', '=', 'funds.id')
            ->leftJoin('companies', 'companies.id', '=', 'companies_funds.company')
            ->select([
                'funds.id',
                'funds.name',
                'funds.start_year',
                'funds.manager_id',
                'funds.created_at',
                'funds.updated_at',
            ])
            ->distinct();

        if (!empty(trim($filter))) {
            $query->where(function ($whereQuery) use ($filter): void {
                $likeFilter = '%'.strtolower($filter).'%';

                $whereQuery->whereRaw('LOWER(funds.name) LIKE ?', [$likeFilter])
                    ->orWhereRaw('LOWER(fund_managers.name) LIKE ?', [$likeFilter])
                    ->orWhereRaw('LOWER(CAST(funds.start_year AS TEXT)) LIKE ?', [$likeFilter])
                    ->orWhereRaw('LOWER(companies.name) LIKE ?', [$likeFilter]);
            });
        }

        return $query
            ->get()
            ->map(fn (object $row) => LaravelFundRepositoryAdapter::fromDB((array) $row))
            ->all();

    }
}
