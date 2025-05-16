<?php

namespace Tinkoff\Invest\Transport;

use Tinkoff\Invest\Logger;
use Tinkoff\Invest\Exceptions\ApiException;
use GuzzleHttp\Exception\RequestException;

class LoggableHttpClient implements HttpClientInterface
{
    private Logger $logger;

    public function __construct(
        private HttpClientInterface $httpClient,
        Logger $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @throws \Throwable
     */
    public function request(string $method, string $uri, array $data = []): array
    {
        $requestId = bin2hex(random_bytes(8));
        $startTime = microtime(true);

        try {
            $this->logger->logApiRequest($requestId, $method, $uri, $data);

            $response = $this->httpClient->request($method, $uri, $data);

            $this->logger->logApiResponse($requestId, $method, $uri, $response, $startTime);

            return $response;
        } catch (RequestException $e) {
            $this->logError($requestId, $method, $uri, $e, $startTime);
            throw ApiException::fromRequestException($e);
        } catch (\Throwable $e) {
            $this->logError($requestId, $method, $uri, $e, $startTime);
            throw $e;
        }
    }

    private function logError(string $requestId, string $method, string $uri, \Throwable $e, float $startTime): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        $this->logger->error('API Error', [
            'request_id' => $requestId,
            'type' => 'error',
            'method' => $method,
            'uri' => $uri,
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'duration_ms' => $duration,
            'trace' => $e->getTraceAsString()
        ]);
    }
}