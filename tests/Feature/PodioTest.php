<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Podio\Client\Endpoints\AppsEndpoint;
use Podio\Client\Endpoints\CommentsEndpoint;
use Podio\Client\Endpoints\EmbedEndpoint;
use Podio\Client\Endpoints\FilesEndpoint;
use Podio\Client\Endpoints\HooksEndpoint;
use Podio\Client\Endpoints\ItemsEndpoint;
use Podio\Client\Endpoints\SearchEndpoint;
use Podio\Client\PodioResponse;
use Podio\Client\RateLimitSnapshot;
use Podio\Client\Resources\Item;
use Podio\Laravel\Facades\Podio;
use Podio\Laravel\Podio as PodioBridge;

test('it exposes every endpoint accessor', function () {
    $podio = app(PodioBridge::class);

    expect($podio->apps())->toBeInstanceOf(AppsEndpoint::class)
        ->and($podio->items())->toBeInstanceOf(ItemsEndpoint::class)
        ->and($podio->hooks())->toBeInstanceOf(HooksEndpoint::class)
        ->and($podio->files())->toBeInstanceOf(FilesEndpoint::class)
        ->and($podio->comments())->toBeInstanceOf(CommentsEndpoint::class)
        ->and($podio->search())->toBeInstanceOf(SearchEndpoint::class)
        ->and($podio->embed())->toBeInstanceOf(EmbedEndpoint::class);
});

test('it authenticates then fetches an item through the full laravel stack', function () {
    Http::fake([
        '*/oauth/token' => Http::response(['access_token' => 'tok', 'expires_in' => 3600]),
        '*/item/5' => Http::response(['item_id' => 5, 'app' => ['app_id' => 9]]),
    ]);

    $item = Podio::items()->get(5);

    expect($item)->toBeInstanceOf(Item::class)
        ->and($item->item_id)->toBe(5)
        ->and($item->app->app_id)->toBe(9);

    Http::assertSent(fn ($r) => str_contains($r->url(), '/oauth/token'));
    Http::assertSent(fn ($r) => str_contains($r->url(), '/item/5') && $r->hasHeader('Authorization'));
});

test('it caches the access token after authenticating', function () {
    Http::fake([
        '*/oauth/token' => Http::response(['access_token' => 'tok', 'expires_in' => 3600]),
        '*/item/*' => Http::response(['item_id' => 1, 'app' => ['app_id' => 1]]),
    ]);

    Podio::items()->get(1);

    expect(Cache::store('podio_test')->has(config('podio.cache.key')))->toBeTrue();
});

test('send delegates and returns the raw response', function () {
    Http::fake([
        '*/oauth/token' => Http::response(['access_token' => 'tok', 'expires_in' => 3600]),
        '*' => Http::response('raw-body', 202),
    ]);

    $response = Podio::send('POST', '/test', ['body' => 'payload', 'raw' => true]);

    expect($response)->toBeInstanceOf(PodioResponse::class)
        ->and($response->statusCode())->toBe(202)
        ->and($response->body())->toBe('raw-body');
});

test('rateLimit returns a snapshot reflecting the response headers', function () {
    Http::fake([
        '*/oauth/token' => Http::response(['access_token' => 'tok', 'expires_in' => 3600]),
        '*/item/*' => Http::response(
            ['item_id' => 1, 'app' => ['app_id' => 1]],
            200,
            ['X-Rate-Limit-Limit' => '5000', 'X-Rate-Limit-Remaining' => '4999'],
        ),
    ]);

    $podio = app(PodioBridge::class);
    $podio->items()->get(1);

    expect($podio->rateLimit())->toBeInstanceOf(RateLimitSnapshot::class)
        ->and($podio->rateLimit()->limit())->toBe(5000)
        ->and($podio->rateLimit()->remaining())->toBe(4999);
});

test('an empty cache store falls back to the default store without throwing', function () {
    config(['podio.cache.store' => '']);
    app()->forgetInstance(PodioBridge::class);

    expect(fn () => app(PodioBridge::class))->not->toThrow(Throwable::class);
});
