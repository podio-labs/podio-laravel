# Podio for Laravel

[![Latest Stable Version](https://img.shields.io/packagist/v/podio-labs/podio-laravel)](https://packagist.org/packages/podio-labs/podio-laravel)
[![License](https://img.shields.io/packagist/l/podio-labs/podio-laravel)](https://packagist.org/packages/podio-labs/podio-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/podio-labs/podio-laravel)](https://packagist.org/packages/podio-labs/podio-laravel)

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

### Minimal

Configure your `.env`:

```dotenv
PODIO_CLIENT_ID=your-app-client-id
PODIO_CLIENT_SECRET=your-app-client-secret
PODIO_USERNAME=service@example.com
PODIO_PASSWORD=password
```

### Full

Everything in `config/podio.php`:

| Key | Env | Default                   |
| --- | --- |---------------------------|
| `base_url` | `PODIO_BASE_URL` | `'https://api.podio.com'` |
| `auth.client_id` | `PODIO_CLIENT_ID` | `null`                    |
| `auth.client_secret` | `PODIO_CLIENT_SECRET` | `null`                    |
| `auth.username` | `PODIO_USERNAME` | `null`                    |
| `auth.password` | `PODIO_PASSWORD` | `null`                    |
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

// Comments & embeds
Podio::comments()->create('item', $itemId, ['value' => 'Imported from Dropbox']);
$embed = Podio::embed()->create(['url' => 'https://youtu.be/...']);

// Webhooks
$hooks = Podio::hooks()->getForApp($appId);
$hook = Podio::hooks()->createForApp($appId, ['url' => route('podio.hook'), 'type' => 'item.create']);
Podio::hooks()->verify($hook->hook_id);

// Apps
$app = Podio::apps()->get($appId);
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
