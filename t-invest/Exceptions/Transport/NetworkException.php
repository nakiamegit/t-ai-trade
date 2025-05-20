<?php

namespace Tinkoff\Invest\Exceptions\Transport;

use Tinkoff\Invest\Exceptions\ApiException;
use Throwable;

class NetworkException extends ApiException
{
    public const ERROR_CONNECTION_FAILED = 'connection_failed';
    public const ERROR_SSL = 'ssl_error';
    public const ERROR_TIMEOUT = 'request_timeout';

    public static function connectionFailed(
        string $endpoint,
        float $timeout,
        ?Throwable $previous = null
    ): self {
        return new self(
            "Connection failed to {$endpoint} (timeout {$timeout} sec)",
            [
                'endpoint' => $endpoint,
                'timeout' => $timeout
            ],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_SERVICE_UNAVAILABLE,
            self::ERROR_CONNECTION_FAILED
        );
    }

    public static function sslError(
        string $message,
        array $details = [],
        ?Throwable $previous = null
    ): self {
        return new self(
            "SSL error: {$message}",
            $details,
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_BAD_REQUEST,
            self::ERROR_SSL
        );
    }

    public static function timeout(
        string $url,
        float $timeout,
        ?Throwable $previous = null
    ): self {
        return new self(
            "Timeout {$timeout} sec exceeded for request to {$url}",
            [
                'url' => $url,
                'timeout' => $timeout
            ],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_SERVICE_UNAVAILABLE,
            self::ERROR_TIMEOUT
        );
    }
}
