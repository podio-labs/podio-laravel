# Podio Laravel

[![Tests](https://github.com/podio-labs/podio-laravel/actions/workflows/tests.yml/badge.svg)](https://github.com/podio-labs/podio-laravel/actions/workflows/tests.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/podio-labs/podio-laravel)](https://packagist.org/packages/podio-labs/podio-laravel)
[![License](https://img.shields.io/packagist/l/podio-labs/podio-laravel)](https://packagist.org/packages/podio-labs/podio-laravel)

Talk to the Podio API from Laravel through a clean, testable `Podio` facade — config-driven auth, cached tokens, and Laravel's own HTTP client under the hood.

In this example, we create an item and comment on it — authentication is handled for you:

```php
use Podio\Laravel\Facades\Podio;

$item = Podio::items()->create($appId, [
    'fields' => ['title' => 'New lead'],
]);

Podio::comments()->create('item', $item->item_id, [
    'value' => 'Created from the contact form',
]);
```

> Not using Laravel? See [`podio-labs/podio-client`](https://github.com/podio-labs/podio-client).

## Installation

> Requires PHP 8.3+ and Laravel 12 or 13.

You can install the package via composer:

```bash
composer require podio-labs/podio-laravel
```

That's it — the `PodioServiceProvider` and `Podio` facade are auto-discovered, so you're ready to go.

Optionally, publish the config file:

```bash
php artisan vendor:publish --tag=podio-config
```

## Configuration

### Authentication

Set your Podio API credentials, then pick a method. The token is fetched, cached and refreshed for you.

**Password** — a service account, server-to-server:

```dotenv
PODIO_CLIENT_ID=your-app-client-id
PODIO_CLIENT_SECRET=your-app-client-secret
PODIO_AUTH_METHOD=password
PODIO_USERNAME=service@example.com
PODIO_PASSWORD=password
```

**App** — a single Podio app via its token:

```dotenv
PODIO_CLIENT_ID=your-app-client-id
PODIO_CLIENT_SECRET=your-app-client-secret
PODIO_AUTH_METHOD=app
PODIO_APP_ID=123456
PODIO_APP_TOKEN=your-app-token
```

Read (and persist) the current token:

```php
$token = Podio::authenticate(); // ensure a valid token and return it
$token = Podio::token();        // current token, or null

$token->value();
$token->expiresAt();
$token->refreshToken();
```

### All options

Everything in `config/podio.php`:

| Key | Env | Default                   |
| --- | --- |---------------------------|
| `base_url` | `PODIO_BASE_URL` | `'https://api.podio.com'` |
| `auth.client_id` | `PODIO_CLIENT_ID` | `null`                    |
| `auth.client_secret` | `PODIO_CLIENT_SECRET` | `null`                    |
| `auth.method` | `PODIO_AUTH_METHOD` | `'password'`              |
| `auth.username` | `PODIO_USERNAME` | `null`                    |
| `auth.password` | `PODIO_PASSWORD` | `null`                    |
| `auth.app_id` | `PODIO_APP_ID` | `null`                    |
| `auth.app_token` | `PODIO_APP_TOKEN` | `null`                    |
| `http.timeout` | `PODIO_HTTP_TIMEOUT` | `30`                      |
| `http.connect_timeout` | `PODIO_HTTP_CONNECT_TIMEOUT` | `10`                      |
| `cache.store` | `PODIO_CACHE_STORE` | `env('CACHE_STORE')`      |
| `cache.key` | `PODIO_CACHE_KEY` | `'podio:access_token'`    |

## Usage

### Endpoints

Everything goes through the `Podio` facade.

```php
use Podio\Laravel\Facades\Podio;

// Items
$item = Podio::items()->get($itemId);
$item = Podio::items()->create($appId, ['fields' => ['title' => 'Hello']]);
$item = Podio::items()->update($itemId, ['fields' => ['title' => 'Updated']]);
$total = Podio::items()->getCount($appId);

// Files
$file = Podio::files()->upload($absolutePath, 'photo.jpg');
Podio::files()->attach($file->file_id, ['ref_type' => 'item', 'ref_id' => $itemId]);
$bytes = Podio::files()->getRaw($fileId);

// Comments
Podio::comments()->create('item', $itemId, ['value' => 'Imported from Dropbox']);

// Embeds
$embed = Podio::embed()->create(['url' => 'https://youtu.be/...']);

// Webhooks
$hooks = Podio::hooks()->getForApp($appId);
$hook = Podio::hooks()->createForApp($appId, ['url' => route('podio.hook'), 'type' => 'item.create']);
Podio::hooks()->verify($hook->hook_id);

// Organizations
$organizations = Podio::organizations()->getAll();
$organization = Podio::organizations()->get($orgId);

// Spaces
$space = Podio::spaces()->get($spaceId);

// Apps
$app = Podio::apps()->get($appId);
$apps = Podio::apps()->getForSpace($spaceId);
```

### Raw requests

For anything not covered by an endpoint, send a request directly:

```php
$response = Podio::send('GET', '/item/123', ['raw' => true]);

$response->statusCode();
$response->body();
$response->rateLimit();
```

### Rate limit

```php
Podio::rateLimit()->limit();
Podio::rateLimit()->remaining();
```

## How it works

`Podio\Laravel\Podio` builds a `Podio\Client\PodioClient` from your config and:

- transports requests through Laravel's `Http` facade via a PSR-18 adapter (`LaravelHttpClient`) — so it honours your HTTP config, fakes, and middleware;
- caches the OAuth access token (`cache.store` / `cache.key`) and reuses it across requests, so you are not re-authenticating on every call.

The facade is bound as a singleton, so the client — and its cached token — is shared for the lifetime of the request.

## Testing

```bash
composer test
```

Because transport runs through the `Http` facade, you can fake Podio in your own tests:

```php
Http::fake([
    'api.podio.com/oauth/token' => Http::response(['access_token' => 'test', 'expires_in' => 3600]),
    'api.podio.com/item/*' => Http::response(['item_id' => 1]),
]);
```

## License

Podio Laravel is open-sourced software licensed under the [MIT license](LICENSE.md).
