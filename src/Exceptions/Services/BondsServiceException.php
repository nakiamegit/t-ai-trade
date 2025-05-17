<?php

namespace Tinkoff\Invest\Exceptions\Services;

use Throwable;
use Tinkoff\Invest\Exceptions\ServiceException;
use DateTimeInterface;

class BondsServiceException extends ServiceException
{
    // Специфичные коды ошибок для сервиса облигаций (начинаем с 450)
    public const ERROR_INVALID_BOND_DATA = 450;
    public const ERROR_INVALID_COUPON_DATA = 451;
    public const ERROR_INVALID_ACCRUED_INTEREST = 452;
    public const ERROR_INVALID_BOND_EVENT = 453;
    public const ERROR_INVALID_ASSET_DATA = 454;

    public static function bondNotFound(string $figi, ?Throwable $previous = null): static
    {
        return new static(
            "Bond not found: {$figi}",
            ['figi' => $figi],
            static::STATUS_NOT_FOUND,
            $previous
        );
    }

    public static function invalidCouponData(array $data, ?Throwable $previous = null): static
    {
        return new static(
            "Invalid bond coupon data structure",
            ['coupon_data' => $data],
            static::ERROR_INVALID_COUPON_DATA,
            $previous
        );
    }

    public static function invalidBondResponse(array $response, ?Throwable $previous = null): static
    {
        return new static(
            "Invalid bond response structure",
            ['api_response' => $response],
            static::ERROR_INVALID_BOND_DATA,
            $previous
        );
    }

    public static function invalidAccruedInterest(array $data, ?Throwable $previous = null): static
    {
        return new static(
            "Invalid accrued interest data",
            ['accrued_interest' => $data],
            static::ERROR_INVALID_ACCRUED_INTEREST,
            $previous
        );
    }

    public static function invalidAssetData(array $data, ?Throwable $previous = null): static
    {
        return new static(
            "Invalid asset bond data",
            ['asset_data' => $data],
            static::ERROR_INVALID_ASSET_DATA,
            $previous
        );
    }

    public static function invalidBondEvent(array $data, ?Throwable $previous = null): static
    {
        return new static(
            "Invalid bond event data",
            ['bond_event' => $data],
            static::ERROR_INVALID_BOND_EVENT,
            $previous
        );
    }

    public static function invalidDateRange(DateTimeInterface $from, DateTimeInterface $to): static
    {
        return new static(
            sprintf('Invalid date range: %s to %s', $from->format('Y-m-d'), $to->format('Y-m-d')),
            [
                'from' => $from->format(DateTimeInterface::ATOM),
                'to' => $to->format(DateTimeInterface::ATOM)
            ],
            static::STATUS_BAD_REQUEST
        );
    }

    public static function bondNotAvailable(string $figi, ?Throwable $previous = null): static
    {
        return new static(
            "Bond not available for trading: {$figi}",
            ['figi' => $figi],
            static::STATUS_FORBIDDEN,
            $previous
        );
    }
}