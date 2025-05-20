<?php

namespace Tinkoff\Invest\Models\Enums;

enum RealExchange: int
{
    case UNSPECIFIED = 0;
    case MOEX = 1;
    case RTS = 2;
    case OTC = 3;

    public static function fromApi(string $apiValue): self
    {
        return match ($apiValue) {
            'REAL_EXCHANGE_MOEX' => self::MOEX,
            'REAL_EXCHANGE_RTS' => self::RTS,
            'REAL_EXCHANGE_OTC' => self::OTC,
            default => self::UNSPECIFIED,
        };
    }

    public function toApi(): string
    {
        return match ($this) {
            self::MOEX => 'REAL_EXCHANGE_MOEX',
            self::RTS => 'REAL_EXCHANGE_RTS',
            self::OTC => 'REAL_EXCHANGE_OTC',
            default => 'REAL_EXCHANGE_UNSPECIFIED',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::MOEX => 'Московская биржа',
            self::RTS => 'Санкт-Петербургская биржа',
            self::OTC => 'Внебиржевой инструмент',
            default => 'Тип не определён',
        };
    }
}
