<?php

namespace Tinkoff\Invest\Models\Enums;

enum OperationState: int
{
    case UNSPECIFIED = 0;
    case EXECUTED = 1;
    case CANCELED = 2;

    public const API_VALUES = [
        'OPERATION_STATE_EXECUTED' => self::EXECUTED->value,
        'OPERATION_STATE_CANCELED' => self::CANCELED->value,
        'OPERATION_STATE_UNSPECIFIED' => self::UNSPECIFIED->value
    ];

    public static function fromApiValue(string $apiValue): self
    {
        if (!isset(self::API_VALUES[$apiValue])) {
            return self::UNSPECIFIED;
        }
        return self::tryFrom(self::API_VALUES[$apiValue]) ?? self::UNSPECIFIED;
    }
}
