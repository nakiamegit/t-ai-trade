<?php

namespace Rest\Transformers;

class BondTransformer
{
    public static function transform($bond): array
    {
        return [
            'figi' => $bond->figi,
            'name' => $bond->name,
            'ticker' => $bond->ticker,
            'lot' => $bond->lot,
            'currency' => $bond->currency,
            'exchange' => $bond->exchange,
            //'coupon' => $bond->coupon,
            'nominal' => $bond->nominal,
            //'issueDate' => $bond->issueDate?->format(\DateTimeInterface::ATOM),
            'maturityDate' => $bond->maturityDate?->format(\DateTimeInterface::ATOM)
        ];
    }
}
