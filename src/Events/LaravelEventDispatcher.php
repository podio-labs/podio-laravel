<?php

namespace Podio\Laravel\Events;

use Illuminate\Support\Facades\Event;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class LaravelEventDispatcher implements EventDispatcherInterface
{
    public function dispatch(object $event): object
    {
        Event::dispatch($event);

        return $event;
    }
}
