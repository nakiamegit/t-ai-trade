<?php

namespace Tinkoff\Invest\Transport;

use Tinkoff\Invest\Logger;
use Tinkoff\Invest\Exceptions\Transport\NetworkException;
use Tinkoff\Invest\Exceptions\Transport\RequestException;

class LoggableHttpClient implements HttpClientInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private Logger $logger
    ) {
    }

    /**
     * @throws \Exception
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
        } catch (RequestException | NetworkException $e) {
            $this->logger->logError($requestId, $e, [
                'method' => $method,
                'uri' => $uri,
                'request_data' => $data,
                'duration' => microtime(true) - $startTime
            ]);
            throw $e;
        } catch (\Throwable $e) {
            $this->logger->logError($requestId, $e, [
                'method' => $method,
                'uri' => $uri,
                'request_data' => $data,
                'duration' => microtime(true) - $startTime
            ]);
            throw NetworkException::connectionFailed(
                $uri,
                0,
                $e
            );
        }
    }

    /**
     * @throws \Exception
     */
    public function get(string $uri, array $data = []): array
    {
        return $this->request('GET', $uri, $data);
    }

    /**
     * @throws \Exception
     */
    public function post(string $uri, array $data = []): array
    {
        return $this->request('POST', $uri, $data);
    }
}
