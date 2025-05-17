<?php

namespace Tinkoff\Invest\Exceptions\Services;

use Tinkoff\Invest\Exceptions\ServiceException;

class AccountServiceException extends ServiceException
{
    private const ERROR_BAD_REQUEST = 400;
    private const ERROR_FORBIDDEN = 403;
    private const ERROR_NOT_FOUND = 404;
    private const ERROR_SERVER_ERROR = 500;
    private const ERROR_SERVICE_UNAVAILABLE = 503;

    public static function accountNotFound(string $accountId): self
    {
        return new self(
            "Account not found: {$accountId}",
            ['account_id' => $accountId],
            self::ERROR_NOT_FOUND
        );
    }

    public static function invalidAccountResponse(array $response, \Throwable $previous = null): self
    {
        return new self(
            "Invalid account response structure",
            ['api_response' => $response],
            self::ERROR_SERVER_ERROR,
            $previous
        );
    }

    public static function accessDenied(string $accountId): self
    {
        return new self(
            "Access denied to account {$accountId}",
            ['account_id' => $accountId],
            self::ERROR_FORBIDDEN
        );
    }

    public static function serviceUnavailable(string $serviceName, \Throwable $previous = null): self
    {
        return new self(
            "Service unavailable for method {$serviceName}",
            ['service' => $serviceName],
            self::ERROR_SERVICE_UNAVAILABLE,
            $previous
        );
    }

    public static function invalidRequest(string $message, array $context = []): self
    {
        return new self(
            "Invalid request: {$message}",
            $context,
            self::ERROR_BAD_REQUEST
        );
    }

    public static function marginAttributesNotFound(string $accountId): self
    {
        return new self(
            "Margin attributes not found for account: {$accountId}",
            ['account_id' => $accountId],
            self::ERROR_NOT_FOUND
        );
    }
}