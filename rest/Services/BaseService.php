<?php

namespace Rest\Services;

use Tinkoff\Invest\InvestClient;

abstract class BaseService
{
    protected InvestClient $client;

    public function __construct(InvestClient $client)
    {
        $this->client = $client;
    }
}
