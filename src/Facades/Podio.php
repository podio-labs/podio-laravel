<?php

namespace Podio\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Podio\Client\Endpoints\AppsEndpoint;
use Podio\Client\Endpoints\CommentsEndpoint;
use Podio\Client\Endpoints\EmbedEndpoint;
use Podio\Client\Endpoints\FilesEndpoint;
use Podio\Client\Endpoints\HooksEndpoint;
use Podio\Client\Endpoints\ItemsEndpoint;
use Podio\Client\Endpoints\SearchEndpoint;
use Podio\Client\PodioResponse;
use Podio\Client\RateLimitSnapshot;

/**
 * @method static AppsEndpoint apps()
 * @method static ItemsEndpoint items()
 * @method static HooksEndpoint hooks()
 * @method static FilesEndpoint files()
 * @method static CommentsEndpoint comments()
 * @method static SearchEndpoint search()
 * @method static EmbedEndpoint embed()
 * @method static PodioResponse send(string $method, string $uri, array $options = [])
 * @method static RateLimitSnapshot rateLimit()
 *
 * @mixin \Podio\Laravel\Podio
 *
 * @see \Podio\Laravel\Podio
 */
final class Podio extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Podio\Laravel\Podio::class;
    }
}
