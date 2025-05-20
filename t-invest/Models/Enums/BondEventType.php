<?php

namespace Tinkoff\Invest\Models\Enums;

/**
 * Типы событий облигаций
 * @see https://developer.tbank.ru/invest/services/instruments/methods#getbondeventsrequesteventtype
 */
enum BondEventType: int
{
    case UNSPECIFIED = 0;
    case COUPON = 1;        // Купон
    case AMORTIZATION = 2;   // Амортизация
    case MATURITY = 3;       // Погашение
    case DEFAULT = 4;        // Дефолт
    case OTHER = 5;          // Другое

    public static function fromApi(string $apiValue): self
    {
        return match ($apiValue) {
            'EVENT_TYPE_COUPON' => self::COUPON,
            'EVENT_TYPE_AMORTIZATION' => self::AMORTIZATION,
            'EVENT_TYPE_MATURITY' => self::MATURITY,
            'EVENT_TYPE_DEFAULT' => self::DEFAULT,
            'EVENT_TYPE_OTHER' => self::OTHER,
            default => self::UNSPECIFIED,
        };
    }

    public function toApi(): string
    {
        return match ($this) {
            self::COUPON => 'EVENT_TYPE_COUPON',
            self::AMORTIZATION => 'EVENT_TYPE_AMORTIZATION',
            self::MATURITY => 'EVENT_TYPE_MATURITY',
            self::DEFAULT => 'EVENT_TYPE_DEFAULT',
            self::OTHER => 'EVENT_TYPE_OTHER',
            default => 'EVENT_TYPE_UNSPECIFIED',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::COUPON => 'Выплата купона',
            self::AMORTIZATION => 'Амортизация облигации',
            self::MATURITY => 'Погашение облигации',
            self::DEFAULT => 'Дефолт эмитента',
            self::OTHER => 'Другое событие',
            default => 'Тип события не определён',
        };
    }
}
