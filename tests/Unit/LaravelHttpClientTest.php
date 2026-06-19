<?php

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;
use Podio\Laravel\Http\LaravelHttpClient;
use Psr\Http\Message\ResponseInterface;

test('it forwards method, uri and body and returns a psr-7 response', function () {
    Http::fake(['*' => Http::response('accepted', 202, ['X-Test' => 'yes'])]);

    $request = new Request('POST', 'https://api.podio.com/item/1', ['Content-Type' => 'application/json'], '{"a":1}');

    $response = (new LaravelHttpClient)->sendRequest($request);

    expect($response)->toBeInstanceOf(ResponseInterface::class)
        ->and($response->getStatusCode())->toBe(202)
        ->and((string) $response->getBody())->toBe('accepted')
        ->and($response->getHeaderLine('X-Test'))->toBe('yes');

    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && $r->url() === 'https://api.podio.com/item/1'
        && $r->body() === '{"a":1}');
});

test('it flattens multi-value headers into a comma-joined string', function () {
    Http::fake(['*' => Http::response('ok')]);

    $request = new Request('GET', 'https://api.podio.com/ping', ['X-Multi' => ['a', 'b']]);

    (new LaravelHttpClient)->sendRequest($request);

    Http::assertSent(fn ($r) => $r->hasHeader('X-Multi', 'a, b'));
});

test('it defaults the content-type to application/json when the request omits it', function () {
    Http::fake(['*' => Http::response('ok')]);

    $request = new Request('POST', 'https://api.podio.com/item', [], '{"x":1}');

    (new LaravelHttpClient)->sendRequest($request);

    Http::assertSent(fn ($r) => $r->hasHeader('Content-Type', 'application/json'));
});

test('it sends neither body nor content-type for a bodyless request', function () {
    Http::fake(['*' => Http::response('ok')]);

    (new LaravelHttpClient)->sendRequest(new Request('GET', 'https://api.podio.com/ping'));

    Http::assertSent(fn ($r) => $r->body() === '' && ! $r->hasHeader('Content-Type'));
});
