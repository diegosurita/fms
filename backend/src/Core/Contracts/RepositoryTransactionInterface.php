<?php

namespace FMS\Core\Contracts;

interface RepositoryTransactionInterface
{
    public function startTransaction(): void;

    public function commitTransaction(): void;

    public function rollbackTransaction(): void;
}