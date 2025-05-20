<?php

namespace Tinkoff\Invest\Models\Enums;

/**
 * Типы аккаунтов
 * @see https://russianinvestments.github.io/investAPI/users/#accountstatus
 */
enum RiskLevel: int
{
    case HIGH = 0;
    case MODERATE = 1;
    case LOW = 2;
    case UNSPECIFIED = 3;

    public static function fromApi(string $apiValue): self
    {
        return match ($apiValue) {
            'RISK_LEVEL_HIGH' => self::HIGH,
            'RISK_LEVEL_MODERATE' => self::MODERATE,
            'RISK_LEVEL_LOW' => self::LOW,
            'RISK_LEVEL_UNSPECIFIED' => self::UNSPECIFIED
        };
    }

    public function toApi(): string
    {
        return match ($this) {
            self::HIGH => 'RISK_LEVEL_HIGH',
            self::MODERATE => 'RISK_LEVEL_MODERATE',
            self::LOW => 'RISK_LEVEL_LOW',
            self::UNSPECIFIED => 'RISK_LEVEL_UNSPECIFIED'
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::HIGH => 'Высокий',
            self::MODERATE => 'Средний',
            self::LOW => 'Низкий',
            self::UNSPECIFIED => 'Не указан'
        };
    }
}
