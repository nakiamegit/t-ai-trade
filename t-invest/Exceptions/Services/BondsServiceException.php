<?php

namespace Tinkoff\Invest\Exceptions\Services;

use Throwable;
use DateTimeInterface;
use Tinkoff\Invest\Exceptions\ServiceException;

class BondsServiceException extends ServiceException
{
    public const ERROR_INVALID_BOND_DATA = 'invalid_bond_data';
    public const ERROR_INVALID_COUPON_DATA = 'invalid_coupon_data';
    public const ERROR_INVALID_ACCRUED_INTEREST = 'invalid_accrued_interest';
    public const ERROR_INVALID_BOND_EVENT = 'invalid_bond_event';
    public const ERROR_INVALID_ASSET_DATA = 'invalid_asset_data';
    public const ERROR_BOND_NOT_AVAILABLE = 'bond_not_available';
    public const ERROR_BOND_NOT_FOUND = 'bond_not_found';
    public const ERROR_INVALID_DATE_RANGE = 'invalid_date_range';

    public static function bondNotFound(
        string $figi,
        ?Throwable $previous = null
    ): self {
        return new self(
            "Bond not found: {$figi}",
            ['figi' => $figi],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_NOT_FOUND,
            self::ERROR_BOND_NOT_FOUND
        );
    }

    public static function invalidCouponData(
        array $data,
        ?Throwable $previous = null
    ): self {
        return new self(
            "Invalid bond coupon data structure",
            ['coupon_data' => $data],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_COUPON_DATA
        );
    }

    public static function invalidBondResponse(
        array $response,
        ?Throwable $previous = null
    ): self {
        return new self(
            "Invalid bond response structure",
            ['api_response' => $response],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_INTERNAL_ERROR,
            self::ERROR_INVALID_BOND_DATA
        );
    }

    public static function invalidAccruedInterest(
        array $data,
        ?Throwable $previous = null
    ): self {
        return new self(
            "Invalid accrued interest data",
            ['accrued_interest' => $data],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_ACCRUED_INTEREST
        );
    }

    public static function invalidAssetData(
        array $data,
        ?Throwable $previous = null
    ): self {
        return new self(
            "Invalid asset bond data",
            ['asset_data' => $data],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_ASSET_DATA
        );
    }

    public static function invalidBondEvent(
        array $data,
        ?Throwable $previous = null
    ): self {
        return new self(
            "Invalid bond event data",
            ['bond_event' => $data],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_BOND_EVENT
        );
    }

    public static function invalidDateRange(
        DateTimeInterface $from,
        DateTimeInterface $to
    ): self {
        return new self(
            sprintf(
                'Invalid date range: %s to %s',
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

    public static function bondNotAvailable(
        string $figi,
        ?Throwable $previous = null
    ): self {
        return new self(
            "Bond not available for trading: {$figi}",
            ['figi' => $figi],
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_FORBIDDEN,
            self::ERROR_BOND_NOT_AVAILABLE
        );
    }
}
