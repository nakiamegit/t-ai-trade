<?php
// src/Transport/HttpClient.php

namespace Tinkoff\Invest\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Tinkoff\Invest\Exceptions\ApiException;

class HttpClient
{
    private Client $client;
    private string $baseUrl;
    private string $token;
    private int $timeout;

    public function __construct(string $baseUrl, string $token, int $timeout = 10)
    {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
        $this->token = $token;
        $this->timeout = $timeout;

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $this->timeout,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
    }

    public function request(string $method, string $uri, array $data = []): array
    {
        try {
            $response = $this->client->post($uri, [
                'json' => empty($data) ? (object)[] : $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new ApiException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}