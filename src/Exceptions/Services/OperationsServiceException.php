<?php

namespace Tinkoff\Invest\Exceptions\Services;

use Tinkoff\Invest\Exceptions\ServiceException;
use DateTimeInterface;

class OperationsServiceException extends ServiceException
{
    public const ERROR_INVALID_DATE_RANGE = 'invalid_date_range';
    public const ERROR_OPERATION_NOT_FOUND = 'operation_not_found';
    public const ERROR_INVALID_RESPONSE = 'invalid_operations_response';
    public const ERROR_ACCOUNT_NOT_FOUND = 'account_not_found';
    public const ERROR_INVALID_STATE = 'invalid_operation_state';
    public const ERROR_INVALID_TYPE = 'invalid_operation_type';
    public const ERROR_INSTRUMENT_NOT_FOUND = 'instrument_not_found';
    public const ERROR_ACCESS_DENIED = 'operations_access_denied';
    public const ERROR_INVALID_CURSOR = 'invalid_cursor';
    public const ERROR_INVALID_LIMIT = 'invalid_limit';
    public const ERROR_MISSING_FIELD = 'missing_required_field';
    public const ERROR_INVALID_TRADE_DATA = 'invalid_trade_data';

    public static function invalidDateRange(DateTimeInterface $from, DateTimeInterface $to): self
    {
        return new self(
            sprintf('Invalid date range: from %s to %s',
                $from->format('Y-m-d'),
                $to->format('Y-m-d')
            ),
            [
                'from' => $from->format(DateTimeInterface::ATOM),
                'to' => $to->format(DateTimeInterface::ATOM)
            ],
            0,
            null,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_DATE_RANGE
        );
    }

    public static function operationNotFound(string $operationId): self
    {
        return new self(
            "Operation not found: {$operationId}",
            ['operation_id' => $operationId],
            0,
            null,
            self::HTTP_NOT_FOUND,
            self::ERROR_OPERATION_NOT_FOUND
        );
    }

    public static function invalidOperationsResponse(array $response): self
    {
        return new self(
            "Invalid operations response structure",
            ['api_response' => $response],
            0,
            null,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_RESPONSE
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

    public static function invalidOperationState(string $state): self
    {
        return new self(
            "Invalid operation state: {$state}",
            ['state' => $state],
            0,
            null,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_STATE
        );
    }

    public static function invalidOperationType(string $type): self
    {
        return new self(
            "Invalid operation type: {$type}",
            ['type' => $type],
            0,
            null,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_TYPE
        );
    }

    public static function instrumentNotFound(string $figi): self
    {
        return new self(
            "Instrument not found: {$figi}",
            ['figi' => $figi],
            0,
            null,
            self::HTTP_NOT_FOUND,
            self::ERROR_INSTRUMENT_NOT_FOUND
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

    public static function invalidCursor(string $cursor): self
    {
        return new self(
            "Invalid cursor value: {$cursor}",
            ['cursor' => $cursor],
            0,
            null,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_CURSOR
        );
    }

    public static function invalidLimit(int $limit): self
    {
        return new self(
            "Invalid limit value: {$limit}. Must be between 1 and 1000",
            ['limit' => $limit],
            0,
            null,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_LIMIT
        );
    }

    public static function missingRequiredField(string $fieldName): self
    {
        return new self(
            "Missing required field: {$fieldName}",
            ['field' => $fieldName],
            0,
            null,
            self::HTTP_BAD_REQUEST,
            self::ERROR_MISSING_FIELD
        );
    }

    public static function invalidTradeData(array $tradeData): self
    {
        return new self(
            "Invalid trade data structure",
            ['trade_data' => $tradeData],
            0,
            null,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_TRADE_DATA
        );
    }
}