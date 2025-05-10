<?php

namespace Tinkoff\Invest\Models\Enums;

enum PositionType: string
{
    case BOND = 'bond';
    case SHARE = 'share';
    case CURRENCY = 'currency';
    case ETF = 'etf';
    case FUTURES = 'futures';
    case UNSPECIFIED = 'unspecified';

    public static function fromApi(string $apiValue): self
    {
        return match ($apiValue) {
            'bond' => self::BOND,
            'share' => self::SHARE,
            'currency' => self::CURRENCY,
            'etf' => self::ETF,
            'futures' => self::FUTURES,
            default => self::UNSPECIFIED,
        };
    }

    public function toApi(): string
    {
        return $this->value;
    }

    public function description(): string
    {
        return match ($this) {
            self::BOND => 'Облигация',
            self::SHARE => 'Акция',
            self::CURRENCY => 'Валюта',
            self::ETF => 'Фонд',
            self::FUTURES => 'Фьючерс',
            default => 'Тип инструмента не определён',
        };
    }
}