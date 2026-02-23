<?php

namespace FMS\Core\Contracts;

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
}