<?php

namespace Tinkoff\Invest\Exceptions\Services;

use Tinkoff\Invest\Exceptions\ServiceException;

class AccountServiceException extends ServiceException
{
    public static function accountNotFound(string $accountId): self
    {
        return new self(
            "Account not found: {$accountId}",
            ['account_id' => $accountId],
            static::STATUS_NOT_FOUND
        );
    }

    public static function invalidAccountResponse(array $response, \Throwable $previous = null): self
    {
        return new self(
            "Invalid account response structure",
            ['api_response' => $response],
            static::STATUS_SERVER_ERROR,
            $previous
        );
    }

    public static function accessDenied(string $accountId): self
    {
        return new self(
            "Access denied to account {$accountId}",
            ['account_id' => $accountId],
            static::STATUS_FORBIDDEN
        );
    }

    public static function serviceUnavailable(string $serviceName, \Throwable $previous = null): static
    {
        return new static(
            "Service unavailable for method {$serviceName}",
            ['service' => $serviceName],
            static::STATUS_SERVICE_UNAVAILABLE,
            $previous
        );
    }

    public static function invalidRequest(string $message, array $context = []): self
    {
        return new self(
            "Invalid request: {$message}",
            $context,
            static::STATUS_BAD_REQUEST
        );
    }

    public static function marginAttributesNotFound(string $accountId): self
    {
        return new self(
            "Margin attributes not found for account: {$accountId}",
            ['account_id' => $accountId],
            static::STATUS_NOT_FOUND
        );
    }
}