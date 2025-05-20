<?php

namespace Tinkoff\Invest\Exceptions\Transport;

use Tinkoff\Invest\Exceptions\ApiException;
use Throwable;

class RequestException extends ApiException
{
    public const ERROR_MALFORMED_RESPONSE = 'malformed_response';
    public const ERROR_INVALID_REQUEST = 'invalid_request';
    public const ERROR_HTTP = 'http_error';
    public const ERROR_RATE_LIMIT = 'rate_limit_exceeded';

    public static function malformedResponse(
        string $message,
        array $context = [],
        ?Throwable $previous = null
    ): self {
        return new self(
            "Malformed API response: {$message}",
            $context,
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_BAD_REQUEST,
            self::ERROR_MALFORMED_RESPONSE
        );
    }

    public static function invalidRequest(
        string $message,
        array $requestData = [],
        ?Throwable $previous = null
    ): self {
        return new self(
            "Invalid request: {$message}",
            ['request' => $requestData],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_REQUEST
        );
    }

    public static function fromHttpError(
        int $statusCode,
        string $reason,
        array $response = []
    ): self {
        $message = "HTTP {$statusCode}";
        if ($reason) {
            $message .= ": {$reason}";
        }

        return new self(
            $message,
            ['response' => $response],
            0,
            null,
            $statusCode,
            self::ERROR_HTTP
        );
    }

    public static function rateLimitExceeded(int $retryAfter): self
    {
        return new self(
            "Rate limit exceeded. Try again in {$retryAfter} sec",
            ['retry_after' => $retryAfter],
            0,
            null,
            self::HTTP_TOO_MANY_REQUESTS,
            self::ERROR_RATE_LIMIT
        );
    }
}
