<?php

namespace FMS\Infrastructure\Repositories;

use FMS\Core\Contracts\CompanyRepository;
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
}
