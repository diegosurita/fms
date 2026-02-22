<?php

namespace FMS\Infrastructure\Repositories;

use FMS\Core\Contracts\FundRepository;
use FMS\Core\DataTransferObjects\SaveFundDTO;
use FMS\Core\Entities\FundEntity;
use FMS\Infrastructure\Adapter\LaravelFundRepositoryAdapter;
use Illuminate\Support\Facades\DB;

class LaravelFundRepository extends LaravelRepository implements FundRepository
{
    public function list(?string $filter = null): array
    {
        $query = DB::table('funds')
            ->join('fund_managers', 'fund_managers.id', '=', 'funds.manager_id')
            ->leftJoin('companies_funds', 'companies_funds.fund', '=', 'funds.id')
            ->leftJoin('companies', 'companies.id', '=', 'companies_funds.company')
            ->whereNull('funds.deleted_at')
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

    public function create(SaveFundDTO $saveFundDTO): FundEntity
    {
        $fundId = DB::table('funds')->insertGetId([
            'name' => $saveFundDTO->name,
            'start_year' => $saveFundDTO->startYear,
            'manager_id' => $saveFundDTO->managerId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /** @var object $fund */
        $fund = DB::table('funds')
            ->select([
                'id',
                'name',
                'start_year',
                'manager_id',
                'created_at',
                'updated_at',
            ])
            ->where('id', $fundId)
            ->first();

        return LaravelFundRepositoryAdapter::fromDB((array) $fund);
    }

    public function update(SaveFundDTO $saveFundDTO): FundEntity
    {
        if ($saveFundDTO->id === null) {
            throw new \InvalidArgumentException('Fund id is required to update fund.');
        }

        DB::table('funds')
            ->where('id', $saveFundDTO->id)
            ->update([
                'name' => $saveFundDTO->name,
                'start_year' => $saveFundDTO->startYear,
                'manager_id' => $saveFundDTO->managerId,
                'updated_at' => now(),
            ]);

        /** @var object|null $fund */
        $fund = DB::table('funds')
            ->select([
                'id',
                'name',
                'start_year',
                'manager_id',
                'created_at',
                'updated_at',
            ])
            ->where('id', $saveFundDTO->id)
            ->first();

        if ($fund === null) {
            throw new \RuntimeException('Fund not found.'); // TODO: return null instead of throwing exception
        }

        return LaravelFundRepositoryAdapter::fromDB((array) $fund);
    }

    public function delete(int $id): bool
    {
        return DB::table('funds')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]) > 0;
    }
}
