<?php

namespace Tinkoff\Invest\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Tinkoff\Invest\Exceptions\Transport\RequestException;
use Tinkoff\Invest\Exceptions\Transport\NetworkException;

class HttpClient implements HttpClientInterface
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

        try {
            $this->client = new Client([
                'base_uri' => $this->baseUrl,
                'timeout' => $this->timeout,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);
        } catch (\Throwable $e) {
            throw NetworkException::connectionFailed(
                $this->baseUrl,
                $this->timeout,
                $e
            );
        }
    }

    public function request(string $method, string $uri, array $data = []): array
    {
        try {
            $response = $this->client->request($method, $uri, [
                'json' => empty($data) ? (object)[] : $data
            ]);

            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw RequestException::malformedResponse(
                    json_last_error_msg(),
                    [
                        'response_body' => $responseBody,
                        'json_error' => json_last_error()
                    ]
                );
            }

            return $responseData;

        } catch (GuzzleException $e) {
            $this->handleGuzzleException($e);
        }
    }

    private function handleGuzzleException(GuzzleException $e): void
    {
        if ($e instanceof \GuzzleHttp\Exception\RequestException && $e->getResponse()) {
            $response = $e->getResponse();
            $responseData = [];

            try {
                $responseData = json_decode($response->getBody()->getContents(), true);
            } catch (\Throwable $parseError) {
                $responseData = ['error' => 'Failed to parse response: ' . $parseError->getMessage()];
            }

            throw RequestException::fromHttpError(
                $response->getStatusCode(),
                $e->getMessage(),
                $responseData
            );
        }

        throw NetworkException::connectionFailed(
            $this->baseUrl,
            $this->timeout,
            $e
        );
    }

    public function get(string $uri, array $data = []): array
    {
        return $this->request('GET', $uri, $data);
    }

    public function post(string $uri, array $data = []): array
    {
        return $this->request('POST', $uri, $data);
    }
}