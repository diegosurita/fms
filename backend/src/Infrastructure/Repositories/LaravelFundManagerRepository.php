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

    public function update(SaveFundManagerDTO $saveFundManagerDTO): FundManagerEntity
    {
        if ($saveFundManagerDTO->id === null) {
            throw new \InvalidArgumentException('Fund manager id is required to update fund manager.', 400);
        }

        DB::table('fund_managers')
            ->where('id', $saveFundManagerDTO->id)
            ->whereNull('deleted_at')
            ->update([
                'name' => $saveFundManagerDTO->name,
                'updated_at' => now(),
            ]);

        /** @var object|null $fundManager */
        $fundManager = DB::table('fund_managers')
            ->select([
                'id',
                'name',
                'created_at',
                'updated_at',
            ])
            ->where('id', $saveFundManagerDTO->id)
            ->whereNull('deleted_at')
            ->first();

        if ($fundManager === null) {
            throw new \RuntimeException('Fund manager not found.', 404);
        }

        return LaravelFundManagerEntityAdapter::fromDB((array) $fundManager);
    }

    public function delete(int $id): bool
    {
        return DB::table('fund_managers')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]) > 0;
    }

    public function hasActiveFunds(int $id): bool
    {
        return DB::table('funds')
            ->where('manager_id', $id)
            ->whereNull('deleted_at')
            ->exists();
    }
}
