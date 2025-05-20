<?php

namespace Tinkoff\Invest;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Tinkoff\Invest\Exceptions\LoggerException;

class Logger implements LoggerInterface
{
    use LoggerTrait;

    private $logFile;
    private bool $logSensitiveData;
    private bool $fullLogging;
    private array $sensitiveParams = ['token', 'password', 'authorization', 'api_key'];

    public function __construct(Config $config)
    {
        $this->logSensitiveData = $config->isLoggingSensitiveData();
        $this->fullLogging = $config->isLoggingFull();

        if (!$config->isLoggingEnabled()) {
            return;
        }

        $logPath = $config->getLogPath();
        $dir = dirname($logPath);

        try {
            if (!is_dir($dir)) {
                if (!@mkdir($dir, 0755, true) && !is_dir($dir)) {
                    throw LoggerException::logDirectoryCreationFailed($dir);
                }
            }

            $this->logFile = @fopen($logPath, 'ab');
            if ($this->logFile === false) {
                throw LoggerException::logFileOpenFailed($logPath);
            }
        } catch (\Throwable $e) {
            throw LoggerException::logDirectoryCreationFailed(
                $logPath,
                [
                    'error' => $e->getMessage(),
                    'logging_enabled' => $config->isLoggingEnabled(),
                    'log_path' => $logPath
                ],
                $e
            );
        }
    }

    public function log($level, $message, array $context = []): void
    {
        if (!$this->logFile) {
            throw LoggerException::resourceNotAvailable();
        }

        $record = [
            'timestamp' => date('Y-m-d H:i:s.v'),
            'level' => $level,
            'message' => $message,
            'context' => $this->sanitizeContext($context)
        ];

        try {
            $logLine = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
            if (fwrite($this->logFile, $logLine) === false) {
                throw LoggerException::logWriteFailed("Failed to write log entry");
            }
        } catch (\Throwable $e) {
            throw LoggerException::logWriteFailed(
                $e->getMessage(),
                [
                    'log_record' => $record,
                    'error' => $e->getMessage()
                ],
                $e
            );
        }
    }

    public function logApiRequest(string $requestId, string $method, string $uri, array $params): void
    {
        $this->info('API Request', [
            'request_id' => $requestId,
            'method' => $method,
            'uri' => $this->shortenUri($uri),
            'params' => $this->sanitizeParams($params)
        ]);
    }

    public function logApiResponse(
        string $requestId,
        string $method,
        string $uri,
        array $response,
        float $startTime
    ): void {
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        $logData = [
            'request_id' => $requestId,
            'method' => $method,
            'uri' => $this->shortenUri($uri),
            'duration_ms' => $duration,
            'status' => $response['status'] ?? 'success'
        ];

        if ($this->fullLogging) {
            $logData['response'] = $response;
        } else {
            $logData['response_summary'] = $this->summarizeResponse($response);
        }

        $this->info('API Response', $logData);
    }

    public function logError(string $requestId, \Throwable $e, array $context = []): void
    {
        $errorData = [
            'request_id' => $requestId,
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];

        if ($this->fullLogging) {
            $errorData['trace'] = $this->filterTrace($e->getTrace());
        }

        $this->error(get_class($e), array_merge($errorData, $context));
    }

    private function shortenUri(string $uri): string
    {
        return str_replace('tinkoff.public.invest.api.contract.v1.', '', $uri);
    }

    private function sanitizeParams(array $params): array
    {
        if ($this->logSensitiveData) {
            return $params;
        }

        array_walk_recursive($params, function (&$value, $key) {
            if (in_array(strtolower($key), $this->sensitiveParams, true)) {
                $value = '***REDACTED***';
            }
        });

        return $params;
    }

    private function sanitizeContext(array $context): array
    {
        if ($this->logSensitiveData) {
            return $context;
        }

        array_walk_recursive($context, function (&$value, $key) {
            if (in_array(strtolower($key), $this->sensitiveParams, true)) {
                $value = '***REDACTED***';
            }
        });

        return $context;
    }

    private function filterTrace(array $trace): array
    {
        return array_map(function ($item) {
            return [
                'file' => $item['file'] ?? null,
                'line' => $item['line'] ?? null,
                'class' => $item['class'] ?? null,
                'function' => $item['function'] ?? null
            ];
        }, $trace);
    }

    private function summarizeResponse(array $response): array
    {
        $payload = $response['payload'] ?? [];
        return [
            'items_count' => is_countable($payload) ? count($payload) : 0,
            'first_item' => $payload[0] ?? null
        ];
    }

    public function __destruct()
    {
        if (is_resource($this->logFile)) {
            fclose($this->logFile);
        }
    }
}
