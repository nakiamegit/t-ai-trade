<?php

namespace Tinkoff\Invest\Models\Enums;

/**
 * Типы аккаунтов
 * @see https://russianinvestments.github.io/investAPI/users/#accounttype
 */
enum AccountType: int
{
    case UNSPECIFIED = 0;
    case TINKOFF = 1;
    case IIS = 2;
    case INVEST_BOX = 3;
    case INVEST_FUND = 4;

    public static function fromApi(string $apiValue): self
    {
        return match ($apiValue) {
            'ACCOUNT_TYPE_TINKOFF' => self::TINKOFF,
            'ACCOUNT_TYPE_TINKOFF_IIS' => self::IIS,
            'ACCOUNT_TYPE_INVEST_BOX' => self::INVEST_BOX,
            'ACCOUNT_TYPE_INVEST_FUND' => self::INVEST_FUND,
            default => self::UNSPECIFIED,
        };
    }

    public function toApi(): string
    {
        return match ($this) {
            self::TINKOFF => 'ACCOUNT_TYPE_TINKOFF',
            self::IIS => 'ACCOUNT_TYPE_TINKOFF_IIS',
            self::INVEST_BOX => 'ACCOUNT_TYPE_INVEST_BOX',
            self::INVEST_FUND => 'ACCOUNT_TYPE_INVEST_FUND',
            default => 'ACCOUNT_TYPE_UNSPECIFIED',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::TINKOFF => 'Брокерский счёт Т-Инвестиций',
            self::IIS => 'ИИС',
            self::INVEST_BOX => 'Инвесткопилка',
            self::INVEST_FUND => 'Фонд денежного рынка',
            default => 'Тип аккаунта не определён',
        };
    }
}
