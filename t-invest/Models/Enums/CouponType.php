<?php

namespace Tinkoff\Invest\Models\Enums;

enum CouponType: int
{
    case UNSPECIFIED = 0;
    case CONSTANT = 1;
    case FLOATING = 2;
    case DISCOUNT = 3;
    case MORTGAGE = 4;
    case FIX = 5;
    case VARIABLE = 6;
    case OTHER = 7;

    public static function fromApi(string $apiValue): self
    {
        return match ($apiValue) {
            'COUPON_TYPE_CONSTANT' => self::CONSTANT,
            'COUPON_TYPE_FLOATING' => self::FLOATING,
            'COUPON_TYPE_DISCOUNT' => self::DISCOUNT,
            'COUPON_TYPE_MORTGAGE' => self::MORTGAGE,
            'COUPON_TYPE_FIX' => self::FIX,
            'COUPON_TYPE_VARIABLE' => self::VARIABLE,
            'COUPON_TYPE_OTHER' => self::OTHER,
            default => self::UNSPECIFIED,
        };
    }

    public function toApi(): string
    {
        return match ($this) {
            self::CONSTANT => 'COUPON_TYPE_CONSTANT',
            self::FLOATING => 'COUPON_TYPE_FLOATING',
            self::DISCOUNT => 'COUPON_TYPE_DISCOUNT',
            self::MORTGAGE => 'COUPON_TYPE_MORTGAGE',
            self::FIX => 'COUPON_TYPE_FIX',
            self::VARIABLE => 'COUPON_TYPE_VARIABLE',
            self::OTHER => 'COUPON_TYPE_OTHER',
            default => 'COUPON_TYPE_UNSPECIFIED',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::CONSTANT => 'Постоянный',
            self::FLOATING => 'Плавающий',
            self::DISCOUNT => 'Дисконт',
            self::MORTGAGE => 'Ипотечный',
            self::FIX => 'Фиксированный',
            self::VARIABLE => 'Переменный',
            self::OTHER => 'Прочее',
            default => 'Неопределенное значение',
        };
    }
}
