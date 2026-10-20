<?php

use Illuminate\Support\Facades\Event;
use Podio\Laravel\Events\LaravelEventDispatcher;

test('it dispatches the event through laravel and returns it', function () {
    Event::fake();
    $event = new stdClass;

    expect((new LaravelEventDispatcher)->dispatch($event))->toBe($event);

    Event::assertDispatched(stdClass::class);
});
