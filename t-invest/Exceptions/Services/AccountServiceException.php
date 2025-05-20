<?php

namespace Tinkoff\Invest\Exceptions\Services;

use Throwable;
use Tinkoff\Invest\Exceptions\ServiceException;

class AccountServiceException extends ServiceException
{
    // Account-specific error types
    public const ERROR_ACCOUNT_NOT_FOUND = 'account_not_found';
    public const ERROR_INVALID_RESPONSE = 'invalid_account_response';
    public const ERROR_MARGIN_DATA = 'margin_data_error';
    public const ERROR_ACCESS_DENIED = 'account_access_denied';

    public static function accountNotFound(string $accountId, ?Throwable $previous = null): self
    {
        return new self(
            "Account not found: {$accountId}",
            ['account_id' => $accountId],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_NOT_FOUND,
            self::ERROR_ACCOUNT_NOT_FOUND
        );
    }

    public static function invalidAccountResponse(
        array $response,
        ?Throwable $previous = null
    ): self {
        return new self(
            "Invalid account response structure",
            ['api_response' => $response],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_INTERNAL_ERROR,
            self::ERROR_INVALID_RESPONSE
        );
    }

    public static function marginAttributesNotFound(
        string $accountId,
        ?Throwable $previous = null
    ): self {
        return new self(
            "Margin attributes not found for account: {$accountId}",
            ['account_id' => $accountId],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_NOT_FOUND,
            self::ERROR_MARGIN_DATA
        );
    }

    public static function accessDenied(
        string $accountId,
        ?Throwable $previous = null
    ): self {
        return new self(
            "Access denied to account {$accountId}",
            ['account_id' => $accountId],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_FORBIDDEN,
            self::ERROR_ACCESS_DENIED
        );
    }
}
