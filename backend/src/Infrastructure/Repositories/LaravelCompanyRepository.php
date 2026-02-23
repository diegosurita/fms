<?php

namespace FMS\Infrastructure\Repositories;

use FMS\Core\Contracts\CompanyRepository;
use FMS\Core\DataTransferObjects\SaveCompanyDTO;
use FMS\Core\Entities\CompanyEntity;
use FMS\Infrastructure\Adapter\LaravelCompanyEntityAdapter;
use Illuminate\Support\Facades\DB;

class LaravelCompanyRepository extends LaravelRepository implements CompanyRepository
{
    public function list(?string $filter = null): array
    {
        $query = DB::table('companies')
            ->whereNull('deleted_at')
            ->select([
                'id',
                'name',
                'created_at',
                'updated_at',
            ]);

        if (!empty(trim($filter))) {
            $query->whereRaw('LOWER(companies.name) LIKE ?', ['%'.strtolower($filter).'%']);
        }

        return $query
            ->get()
            ->map(fn (object $row) => LaravelCompanyEntityAdapter::fromDB((array) $row))
            ->all();
    }

    public function create(SaveCompanyDTO $saveCompanyDTO): CompanyEntity
    {
        $companyId = DB::table('companies')->insertGetId([
            'name' => $saveCompanyDTO->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /** @var object $company */
        $company = DB::table('companies')
            ->select([
                'id',
                'name',
                'created_at',
                'updated_at',
            ])
            ->where('id', $companyId)
            ->first();

        return LaravelCompanyEntityAdapter::fromDB((array) $company);
    }

    public function update(SaveCompanyDTO $saveCompanyDTO): CompanyEntity
    {
        if ($saveCompanyDTO->id === null) {
            throw new \InvalidArgumentException('Company id is required to update company.', 400);
        }

        DB::table('companies')
            ->where('id', $saveCompanyDTO->id)
            ->update([
                'name' => $saveCompanyDTO->name,
                'updated_at' => now(),
            ]);

        /** @var object|null $company */
        $company = DB::table('companies')
            ->select([
                'id',
                'name',
                'created_at',
                'updated_at',
            ])
            ->where('id', $saveCompanyDTO->id)
            ->first();

        if ($company === null) {
            throw new \RuntimeException('Company not found.', 404);
        }

        return LaravelCompanyEntityAdapter::fromDB((array) $company);
    }

    public function exists(int $id): bool
    {
        return DB::table('companies')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * @param int[] $companyIds
     */
    public function syncFundCompanies(int $fundId, array $companyIds): void
    {
        $normalizedCompanyIds = array_values(array_unique(array_filter(array_map(
            static fn (mixed $companyId): int => (int) $companyId,
            $companyIds,
        ), static fn (int $companyId): bool => $companyId > 0)));

        DB::table('companies_funds')
            ->where('fund', $fundId)
            ->delete();

        if ($normalizedCompanyIds === []) {
            return;
        }

        $existingCompanyIds = DB::table('companies')
            ->whereIn('id', $normalizedCompanyIds)
            ->whereNull('deleted_at')
            ->pluck('id')
            ->map(static fn (mixed $companyId): int => (int) $companyId)
            ->all();

        if ($existingCompanyIds === []) {
            return;
        }

        DB::table('companies_funds')->insert(array_map(
            static fn (int $companyId): array => [
                'company' => $companyId,
                'fund' => $fundId,
            ],
            $existingCompanyIds,
        ));
    }

    public function delete(int $id): bool
    {
        return DB::table('companies')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]) > 0;
    }
}
