<?php

namespace Tinkoff\Invest\Exceptions\Services;

use Tinkoff\Invest\Exceptions\ServiceException;

class PortfolioServiceException extends ServiceException
{
    public const ERROR_INVALID_RESPONSE = 'invalid_portfolio_response';
    public const ERROR_POSITION_NOT_FOUND = 'position_not_found';
    public const ERROR_ACCOUNT_NOT_FOUND = 'portfolio_account_not_found';
    public const ERROR_ACCESS_DENIED = 'portfolio_access_denied';
    public const ERROR_MISSING_TOTAL = 'missing_total_amount';
    public const ERROR_INVALID_POSITION = 'invalid_position_data';

    public static function invalidPortfolioResponse(array $response): self
    {
        return new self(
            "Invalid portfolio response structure",
            ['api_response' => $response],
            0,
            null,
            self::HTTP_INTERNAL_ERROR,
            self::ERROR_INVALID_RESPONSE
        );
    }

    public static function positionNotFound(string $figi): self
    {
        return new self(
            "Position not found: {$figi}",
            ['figi' => $figi],
            0,
            null,
            self::HTTP_NOT_FOUND,
            self::ERROR_POSITION_NOT_FOUND
        );
    }

    public static function accountNotFound(string $accountId): self
    {
        return new self(
            "Account not found: {$accountId}",
            ['account_id' => $accountId],
            0,
            null,
            self::HTTP_NOT_FOUND,
            self::ERROR_ACCOUNT_NOT_FOUND
        );
    }

    public static function accessDenied(string $accountId): self
    {
        return new self(
            "Access denied to account {$accountId}",
            ['account_id' => $accountId],
            0,
            null,
            self::HTTP_FORBIDDEN,
            self::ERROR_ACCESS_DENIED
        );
    }

    public static function missingTotalAmount(): self
    {
        return new self(
            "Missing total amount in portfolio response",
            [],
            0,
            null,
            self::HTTP_INTERNAL_ERROR,
            self::ERROR_MISSING_TOTAL
        );
    }

    public static function invalidPositionData(array $positionData): self
    {
        return new self(
            "Invalid position data structure",
            ['position_data' => $positionData],
            0,
            null,
            self::HTTP_INTERNAL_ERROR,
            self::ERROR_INVALID_POSITION
        );
    }
}