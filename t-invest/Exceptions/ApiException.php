<?php

namespace Tinkoff\Invest\Exceptions;

use Throwable;

class ApiException extends BaseException
{
    // HTTP Status Codes
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_CONFLICT = 409;
    public const HTTP_TOO_MANY_REQUESTS = 429;
    public const HTTP_INTERNAL_ERROR = 500;
    public const HTTP_SERVICE_UNAVAILABLE = 503;

    // Error Types
    public const ERROR_VALIDATION = 'validation_error';
    public const ERROR_AUTHENTICATION = 'authentication_error';
    public const ERROR_AUTHORIZATION = 'authorization_error';
    public const ERROR_NOT_FOUND = 'not_found_error';
    public const ERROR_RATE_LIMIT = 'rate_limit_error';
    public const ERROR_SERVER = 'server_error';
    public const ERROR_SERVICE = 'service_error';

    public function __construct(
        string $message,
        array $context = [],
        int $code = 0,
        ?Throwable $previous = null,
        int $httpStatusCode = self::HTTP_INTERNAL_ERROR,
        string $errorType = self::ERROR_SERVER
    ) {
        parent::__construct(
            $message,
            $context,
            $code,
            $previous,
            $httpStatusCode,
            $errorType
        );
    }

    public static function create(
        string $message,
        array $context = [],
        ?Throwable $previous = null,
        int $httpStatusCode = self::HTTP_INTERNAL_ERROR,
        string $errorType = self::ERROR_SERVER
    ): static {
        return new static(
            $message,
            $context,
            $previous?->getCode() ?? 0,
            $previous,
            $httpStatusCode,
            $errorType
        );
    }
}
