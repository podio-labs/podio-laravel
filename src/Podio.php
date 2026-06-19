<?php

namespace Podio\Laravel;

use Illuminate\Support\Facades\Cache;
use Podio\Client\Endpoints\AppsEndpoint;
use Podio\Client\Endpoints\CommentsEndpoint;
use Podio\Client\Endpoints\EmbedEndpoint;
use Podio\Client\Endpoints\FilesEndpoint;
use Podio\Client\Endpoints\HooksEndpoint;
use Podio\Client\Endpoints\ItemsEndpoint;
use Podio\Client\Endpoints\SearchEndpoint;
use Podio\Client\PodioClient;
use Podio\Client\PodioResponse;
use Podio\Client\RateLimitSnapshot;
use Podio\Laravel\Http\LaravelHttpClient;

final class Podio
{
    private PodioClient $client;

    public function __construct()
    {
        $this->client = PodioClient::factory()
            ->withClientCredentials(
                config('podio.auth.client_id'),
                config('podio.auth.client_secret'),
            )
            ->withPasswordAuth(
                config('podio.auth.username'),
                config('podio.auth.password'),
            )
            ->withBaseUrl(config('podio.base_url'))
            ->withHttpClient(new LaravelHttpClient)
            ->withTokenCache(
                cache: Cache::store(config('podio.cache.store') ?: null),
                key: config('podio.cache.key'),
            )
            ->make();
    }

    public function apps(): AppsEndpoint
    {
        return $this->client->apps();
    }

    public function items(): ItemsEndpoint
    {
        return $this->client->items();
    }

    public function hooks(): HooksEndpoint
    {
        return $this->client->hooks();
    }

    public function files(): FilesEndpoint
    {
        return $this->client->files();
    }

    public function comments(): CommentsEndpoint
    {
        return $this->client->comments();
    }

    public function search(): SearchEndpoint
    {
        return $this->client->search();
    }

    public function embed(): EmbedEndpoint
    {
        return $this->client->embed();
    }

    public function rateLimit(): RateLimitSnapshot
    {
        return $this->client->rateLimit();
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function send(string $method, string $uri, array $options = []): PodioResponse
    {
        return $this->client->send($method, $uri, $options);
    }
}
