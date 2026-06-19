<?php

namespace Podio\Laravel\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Podio\Laravel\Facades\Podio;
use Podio\Laravel\PodioServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [PodioServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return ['Podio' => Podio::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('podio.auth.client_id', 'test-client');
        $app['config']->set('podio.auth.client_secret', 'test-secret');
        $app['config']->set('podio.auth.username', 'test-user');
        $app['config']->set('podio.auth.password', 'test-pass');
        $app['config']->set('cache.stores.podio_test', ['driver' => 'array']);
        $app['config']->set('podio.cache.store', 'podio_test');
    }
}
