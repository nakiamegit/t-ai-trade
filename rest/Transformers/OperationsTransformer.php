<?php

namespace Rest\Transformers;

class OperationsTransformer
{
    public static function transform($operation): array
    {
        return [
            'id' => $operation->id,
            'date' => $operation->date->format(\DateTimeInterface::ATOM),
            'type' => $operation->type,
            'state' => $operation->state,
            'payment' => self::formatMoney($operation->payment),
            'price' => self::formatMoney($operation->price),
            'commission' => self::formatMoney($operation->commission),
            'figi' => $operation->figi,
            'instrument_type' => $operation->instrumentType
        ];
    }

    private static function formatMoney($money): array
    {
        return [
            'value' => $money->value,
            'currency' => $money->currency
        ];
    }
}
