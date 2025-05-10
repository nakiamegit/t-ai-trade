<?php

namespace Tinkoff\Invest\Services;

use Tinkoff\Invest\Transport\HttpClient;

class ServiceFactory
{
    public static function createAccountService(HttpClient $httpClient): AccountService
    {
        return new AccountService($httpClient);
    }

    public static function createPortfolioService(HttpClient $httpClient): PortfolioService
    {
        return new PortfolioService($httpClient);
    }

    public static function createOperationsService(HttpClient $httpClient): OperationsService
    {
        return new OperationsService($httpClient);
    }

    public static function createBondsService(HttpClient $httpClient): BondsService
    {
        return new BondsService($httpClient);
    }
}
