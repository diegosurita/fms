<?php

namespace FMS\Infrastructure\Adapter;

use Illuminate\Support\Facades\Event;
use FMS\Core\Contracts\EventDispatcherInterface;

class LaravelEventDispatcherAdapter implements EventDispatcherInterface
{
    public function dispatch(object $event): void
    {
        Event::dispatch($event);
    }
}