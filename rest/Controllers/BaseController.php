<?php

namespace Rest\Controllers;

use Tinkoff\Invest\InvestClient;

abstract class BaseController
{
    protected InvestClient $client;

    public function __construct(InvestClient $client)
    {
        $this->client = $client;
    }

    protected function formatMoney(object $money): array
    {
        return [
            'value' => $money->value,
            'currency' => $money->currency
        ];
    }
}
