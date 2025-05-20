<?php

namespace Tinkoff\Invest\Models\Enums;

/**
 * Типы аккаунтов
 * @see https://russianinvestments.github.io/investAPI/users/#accountstatus
 */
enum AccountStatus: int
{
    case UNSPECIFIED = 0;
    case NEW = 1;
    case OPEN = 2;
    case CLOSED = 3;
    case ALL = 4;

    public static function fromApi(string $apiValue): self
    {
        return match ($apiValue) {
            'ACCOUNT_STATUS_NEW' => self::NEW,
            'ACCOUNT_STATUS_OPEN' => self::OPEN,
            'ACCOUNT_STATUS_CLOSED' => self::CLOSED,
            'ACCOUNT_STATUS_ALL' => self::ALL,
            default => self::UNSPECIFIED,
        };
    }

    public function toApi(): string
    {
        return match ($this) {
            self::NEW => 'ACCOUNT_STATUS_NEW',
            self::OPEN => 'ACCOUNT_STATUS_OPEN',
            self::CLOSED => 'ACCOUNT_STATUS_CLOSED',
            self::ALL => 'ACCOUNT_STATUS_ALL',
            default => 'ACCOUNT_STATUS_UNSPECIFIED',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::NEW => 'Новый, в процессе открытия',
            self::OPEN => 'Открытый и активный счёт',
            self::CLOSED => 'Закрытый счёт',
            self::ALL => 'Все счета',
            default => 'Статус счёта не определён',
        };
    }
}
