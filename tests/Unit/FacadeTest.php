<?php

use Podio\Laravel\Facades\Podio;
use Podio\Laravel\Podio as PodioBridge;

test('the facade resolves to the bridge instance', function () {
    expect(Podio::getFacadeRoot())->toBeInstanceOf(PodioBridge::class);
});
