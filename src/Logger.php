<?php

namespace Tinkoff\Invest;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use RuntimeException;

class Logger implements LoggerInterface
{
    use LoggerTrait;

    private $logFile;
    private bool $logFullResponses;
    private array $sensitiveParams = ['token', 'password', 'authorization'];

    public function __construct(
        Config $config,
        bool $logFullResponses = false
    ) {
        if ($config->isLoggingEnabled()) {
            $logPath = $config->getLogPath();
            $dir = dirname($logPath);

            if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new RuntimeException("Cannot create log directory: {$dir}");
            }

            $this->logFile = fopen($logPath, 'ab');
            if (!is_resource($this->logFile)) {
                throw new RuntimeException("Cannot open log file: {$logPath}");
            }
        }

        $this->logFullResponses = $logFullResponses;
    }

    public function log($level, $message, array $context = []): void
    {
        if (!$this->logFile) {
            return;
        }

        $record = [
            'timestamp' => date('Y-m-d H:i:s.v'),
            'level' => $level,
            'message' => $message,
            'context' => $this->sanitizeContext($context)
        ];

        fwrite($this->logFile, json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL);
    }

    public function logApiRequest(string $requestId, string $method, string $uri, array $params): void
    {
        $this->info('API Request', [
            'request_id' => $requestId,
            'type' => 'request',
            'method' => $method,
            'uri' => $this->shortenUri($uri),
            'params' => $this->sanitizeParams($params)
        ]);
    }

    public function logApiResponse(string $requestId, string $method, string $uri, array $response, float $startTime): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        $logData = [
            'request_id' => $requestId,
            'type' => 'response',
            'method' => $method,
            'uri' => $this->shortenUri($uri),
            'duration_ms' => $duration,
            'status' => $response['status'] ?? 'success'
        ];

        if ($this->logFullResponses) {
            $logData['response'] = $response;
        } else {
            $logData['response_summary'] = $this->summarizeResponse($response);
        }

        $this->info('API Response', $logData);
    }

    private function shortenUri(string $uri): string
    {
        return str_replace('tinkoff.public.invest.api.contract.v1.', '', $uri);
    }

    private function sanitizeParams(array $params): array
    {
        foreach ($params as $key => $value) {
            if (in_array(strtolower($key), $this->sensitiveParams, true)) {
                $params[$key] = '***REDACTED***';
            }
        }
        return $params;
    }

    private function sanitizeContext(array $context): array
    {
        array_walk_recursive($context, function (&$value, $key) {
            if (in_array(strtolower($key), $this->sensitiveParams, true)) {
                $value = '***REDACTED***';
            }
        });
        return $context;
    }

    private function summarizeResponse(array $response): array
    {
        return [
            'items_count' => count($response['payload'] ?? []),
            'first_item' => $response['payload'][0] ?? null
        ];
    }

    public function __destruct()
    {
        if (is_resource($this->logFile)) {
            fclose($this->logFile);
        }
    }
}