<?php

namespace Podio\Laravel\Http;

use Illuminate\Support\Facades\Http;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final readonly class LaravelHttpClient implements ClientInterface
{
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $headers = array_map(fn (array $values): string => implode(', ', $values), $request->getHeaders());

        $pendingRequest = Http::withHeaders($headers)
            ->connectTimeout(config('podio.http.connect_timeout'))
            ->timeout(config('podio.http.timeout'));

        $body = (string) $request->getBody();

        if ($body !== '') {
            $pendingRequest->withBody(
                content: $body,
                contentType: $request->getHeaderLine('Content-Type') ?: 'application/json',
            );
        }

        return $pendingRequest
            ->send($request->getMethod(), (string) $request->getUri())
            ->toPsrResponse();
    }
}
