<?php

namespace Rest\Transformers;

class PortfolioTransformer
{
    public static function transform($position): array
    {
        return [
            'figi' => $position->figi,
            'instrumentType' => $position->instrumentType,
            'quantity' => $position->quantity,
            //'averagePrice' => $position->averagePrice,
            'expectedYield' => $position->expectedYield,
            'currentPrice' => $position->currentPrice,
            'currentNkd' => $position->currentNkd
        ];
    }
}
