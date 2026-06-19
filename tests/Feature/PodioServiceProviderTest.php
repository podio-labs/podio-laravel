<?php

use Illuminate\Support\ServiceProvider;
use Podio\Laravel\Podio;
use Podio\Laravel\PodioServiceProvider;

test('it merges the package config under the podio namespace', function () {
    expect(config('podio.base_url'))->toBe('https://api.podio.com')
        ->and(config('podio.cache.key'))->toBe('podio:access_token');
});

test('it binds the bridge as a singleton', function () {
    expect(app(Podio::class))->toBe(app(Podio::class));
});

test('it registers the config publish group', function () {
    expect(ServiceProvider::$publishGroups)->toHaveKey('podio-config');
});

test('it publishes the package config to the application config path', function () {
    $paths = ServiceProvider::pathsToPublish(PodioServiceProvider::class, 'podio-config');
    $source = array_key_first($paths);

    expect($paths)->toHaveCount(1)
        ->and($source)->toContain('podio-laravel')
        ->and($source)->toEndWith('config/podio.php')
        ->and($paths[$source])->toBe(config_path('podio.php'));
});
