<?php

namespace FMS\Infrastructure\Repositories;

use FMS\Core\Contracts\RepositoryTransactionInterface;
use Illuminate\Support\Facades\DB;

abstract class LaravelRepository implements RepositoryTransactionInterface
{
    public function startTransaction(): void
    {
        DB::beginTransaction();
    }

    public function commitTransaction(): void
    {
        DB::commit();
    }

    public function rollbackTransaction(): void
    {
        DB::rollBack();
    }
}
