<?php

namespace Tinkoff\Invest\Services;

use Tinkoff\Invest\Transport\HttpClientInterface;

class ServiceFactory
{
    public static function createAccountService(HttpClientInterface $httpClient): AccountService
    {
        return new AccountService($httpClient);
    }

    public static function createPortfolioService(HttpClientInterface $httpClient): PortfolioService
    {
        return new PortfolioService($httpClient);
    }

    public static function createOperationsService(HttpClientInterface $httpClient): OperationsService
    {
        return new OperationsService($httpClient);
    }

    public static function createBondsService(HttpClientInterface $httpClient): BondsService
    {
        return new BondsService($httpClient);
    }
}
