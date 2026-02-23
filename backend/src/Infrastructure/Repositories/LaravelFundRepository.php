<?php

namespace FMS\Infrastructure\Repositories;

use FMS\Core\Contracts\FundRepository;
use FMS\Core\DataTransferObjects\SaveFundDTO;
use FMS\Core\Entities\FundEntity;
use FMS\Infrastructure\Adapter\LaravelDuplicatedFundsEntityAdapter;
use FMS\Infrastructure\Adapter\LaravelFundRepositoryAdapter;
use Illuminate\Support\Facades\DB;

class LaravelFundRepository extends LaravelRepository implements FundRepository
{
    public function __construct(private readonly LaravelCompanyRepository $companyRepository)
    {
    }

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

        $rows = $query->get();

        $fundIds = $rows
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        $aliasesByFundId = $this->loadAliasesByFundIds($fundIds);
        $companiesByFundId = $this->loadCompaniesByFundIds($fundIds);

        return $rows
            ->map(function (object $row) use ($aliasesByFundId, $companiesByFundId): FundEntity {
                $data = (array) $row;
                $fundId = isset($data['id']) ? (int) $data['id'] : null;

                $data['aliases'] = $fundId !== null
                    ? ($aliasesByFundId[$fundId] ?? [])
                    : [];
                $data['companies'] = $fundId !== null
                    ? ($companiesByFundId[$fundId] ?? [])
                    : [];

                return LaravelFundRepositoryAdapter::fromDB($data);
            })
            ->all();

    }

    public function create(SaveFundDTO $saveFundDTO): FundEntity
    {
        $aliases = $this->normalizeAliases($saveFundDTO->aliases);
        $companies = $this->normalizeCompanies($saveFundDTO->companies);

        $this->ensureAliasesDoNotExist($aliases);

        $fundId = DB::transaction(function () use ($saveFundDTO, $aliases, $companies): int {
            $fundId = $this->insertFund($saveFundDTO);

            if ($aliases !== []) {
                DB::table('fund_aliases')->insert(array_map(
                    static fn (string $alias): array => [
                        'alias' => $alias,
                        'fund' => $fundId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    $aliases,
                ));
            }

            $this->companyRepository->syncFundCompanies($fundId, $companies);

            return $fundId;
        });

        return $this->findFundOrFail($fundId);
    }

    private function insertFund(SaveFundDTO $saveFundDTO): int
    {
        return DB::table('funds')->insertGetId([
            'name' => $saveFundDTO->name,
            'start_year' => $saveFundDTO->startYear,
            'manager_id' => $saveFundDTO->managerId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function findDuplicateFundId(int $fundId): ?int
    {
        /** @var object|null $fund */
        $fund = DB::table('funds')
            ->select([
                'id',
                'name',
                'manager_id',
            ])
            ->where('id', $fundId)
            ->whereNull('deleted_at')
            ->first();

        if ($fund === null) {
            return null;
        }

        $aliases = DB::table('fund_aliases')
            ->where('fund', $fundId)
            ->pluck('alias')
            ->all();

        $normalizedComparableNames = array_values(array_unique(array_filter(array_map(
            static fn (string $value): string => strtolower(trim($value)),
            [
                (string) $fund->name,
                ...array_map(static fn (mixed $alias): string => (string) $alias, $aliases),
            ],
        ))));

        if ($normalizedComparableNames === []) {
            return null;
        }

        /** @var object|null $duplicate */
        $duplicate = DB::table('funds')
            ->leftJoin('fund_aliases', 'fund_aliases.fund', '=', 'funds.id')
            ->select(['funds.id'])
            ->where('funds.manager_id', (int) $fund->manager_id)
            ->where('funds.id', '!=', $fundId)
            ->whereNull('funds.deleted_at')
            ->where(function ($whereQuery) use ($normalizedComparableNames): void {
                $whereQuery
                    ->whereIn(DB::raw('LOWER(funds.name)'), $normalizedComparableNames)
                    ->orWhereIn(DB::raw('LOWER(fund_aliases.alias)'), $normalizedComparableNames);
            })
            ->distinct()
            ->first();

        if ($duplicate === null) {
            return null;
        }

        return (int) $duplicate->id;
    }

    public function getDuplicated(): array
    {
        return DB::table('duplicated_funds')
            ->join('funds as source_funds', 'source_funds.id', '=', 'duplicated_funds.source_fund_id')
            ->join('funds as duplicated_fund', 'duplicated_fund.id', '=', 'duplicated_funds.duplicated_fund_id')
            ->whereNull('source_funds.deleted_at')
            ->whereNull('duplicated_fund.deleted_at')
            ->select([
                'source_funds.id as source_id',
                'source_funds.name as source_name',
                'source_funds.start_year as source_start_year',
                'source_funds.manager_id as source_manager_id',
                'source_funds.created_at as source_created_at',
                'source_funds.updated_at as source_updated_at',
                'duplicated_fund.id as duplicated_id',
                'duplicated_fund.name as duplicated_name',
                'duplicated_fund.start_year as duplicated_start_year',
                'duplicated_fund.manager_id as duplicated_manager_id',
                'duplicated_fund.created_at as duplicated_created_at',
                'duplicated_fund.updated_at as duplicated_updated_at',
            ])
            ->get()
            ->map(static fn (object $row) => LaravelDuplicatedFundsEntityAdapter::fromDB((array) $row))
            ->all();
    }

    public function registerDuplicatedFund(int $sourceFundId, int $duplicatedFundId): void
    {
        DB::table('duplicated_funds')->insert([
            'source_fund_id' => $sourceFundId,
            'duplicated_fund_id' => $duplicatedFundId,
        ]);
    }

    public function update(SaveFundDTO $saveFundDTO): FundEntity
    {
        if ($saveFundDTO->id === null) {
            throw new \InvalidArgumentException('Fund id is required to update fund.');
        }

        $aliases = $this->normalizeAliases($saveFundDTO->aliases);
        $companies = $this->normalizeCompanies($saveFundDTO->companies);

        $this->ensureAliasesDoNotExist($aliases, $saveFundDTO->id);

        DB::transaction(function () use ($saveFundDTO, $aliases, $companies): void {
            DB::table('funds')
                ->where('id', $saveFundDTO->id)
                ->update([
                    'name' => $saveFundDTO->name,
                    'start_year' => $saveFundDTO->startYear,
                    'manager_id' => $saveFundDTO->managerId,
                    'updated_at' => now(),
                ]);

            DB::table('fund_aliases')
                ->where('fund', $saveFundDTO->id)
                ->delete();

            if ($aliases !== []) {
                DB::table('fund_aliases')->insert(array_map(
                    static fn (string $alias): array => [
                        'alias' => $alias,
                        'fund' => $saveFundDTO->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    $aliases,
                ));
            }

            $this->companyRepository->syncFundCompanies((int) $saveFundDTO->id, $companies);
        });

        return $this->findFundOrFail($saveFundDTO->id);
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

    private function findFundOrFail(int $id): FundEntity
    {
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
            ->where('id', $id)
            ->first();

        if ($fund === null) {
            throw new \RuntimeException('Fund not found.'); // TODO: return null instead of throwing exception
        }

        $data = (array) $fund;
        $data['aliases'] = $this->loadAliasesByFundIds([$id])[$id] ?? [];
        $data['companies'] = $this->loadCompaniesByFundIds([$id])[$id] ?? [];

        return LaravelFundRepositoryAdapter::fromDB($data);
    }

    /**
     * @param string[] $aliases
     *
     * @return string[]
     */
    private function normalizeAliases(array $aliases): array
    {
        return array_values(array_unique(array_filter(array_map(
            static fn (string $alias): string => trim($alias),
            $aliases,
        ), static fn (string $alias): bool => $alias !== '')));
    }

    /**
     * @param string[] $aliases
     */
    private function ensureAliasesDoNotExist(array $aliases, ?int $ignoredFundId = null): void
    {
        if ($aliases === []) {
            return;
        }

        $normalizedAliases = array_map(
            static fn (string $alias): string => strtolower($alias),
            $aliases,
        );

        $query = DB::table('fund_aliases')
            ->whereIn(DB::raw('LOWER(alias)'), $normalizedAliases);

        if ($ignoredFundId !== null) {
            $query->where('fund', '!=', $ignoredFundId);
        }

        $existingAlias = $query->value('alias');

        if ($existingAlias !== null) {
            throw new \InvalidArgumentException('Alias already exists.');
        }
    }

    /**
     * @param int[] $fundIds
     *
     * @return array<int, string[]>
     */
    private function loadAliasesByFundIds(array $fundIds): array
    {
        if ($fundIds === []) {
            return [];
        }

        /** @var array<int, array<int, object{fund:int, alias:string}>> $grouped */
        $grouped = DB::table('fund_aliases')
            ->select(['fund', 'alias'])
            ->whereIn('fund', $fundIds)
            ->get()
            ->groupBy('fund')
            ->all();

        $aliasesByFund = [];

        foreach ($grouped as $fundId => $items) {
            $aliasItems = is_array($items) ? $items : $items->all();

            $aliasesByFund[(int) $fundId] = array_values(array_map(
                static fn (object $item): string => (string) $item->alias,
                $aliasItems,
            ));
        }

        return $aliasesByFund;
    }

    /**
     * @param int[] $companies
     *
     * @return int[]
     */
    private function normalizeCompanies(array $companies): array
    {
        return array_values(array_unique(array_filter(array_map(
            static fn (mixed $companyId): int => (int) $companyId,
            $companies,
        ), static fn (int $companyId): bool => $companyId > 0)));
    }

    /**
     * @param int[] $fundIds
     *
     * @return array<int, int[]>
     */
    private function loadCompaniesByFundIds(array $fundIds): array
    {
        if ($fundIds === []) {
            return [];
        }

        /** @var array<int, array<int, object{fund:int, company:int}>> $grouped */
        $grouped = DB::table('companies_funds')
            ->select(['fund', 'company'])
            ->whereIn('fund', $fundIds)
            ->get()
            ->groupBy('fund')
            ->all();

        $companiesByFund = [];

        foreach ($grouped as $fundId => $items) {
            $companyItems = is_array($items) ? $items : $items->all();

            $companiesByFund[(int) $fundId] = array_values(array_map(
                static fn (object $item): int => (int) $item->company,
                $companyItems,
            ));
        }

        return $companiesByFund;
    }
}
