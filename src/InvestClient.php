<?php

namespace Tinkoff\Invest;

use Tinkoff\Invest\Config;
use Tinkoff\Invest\Logger;
use Tinkoff\Invest\Transport\HttpClient;
use Tinkoff\Invest\Transport\HttpClientInterface;
use Tinkoff\Invest\Transport\LoggableHttpClient;
use Tinkoff\Invest\Services\ServiceFactory;
use Tinkoff\Invest\Services\AccountService;
use Tinkoff\Invest\Services\BondsService;
use Tinkoff\Invest\Services\OperationsService;
use Tinkoff\Invest\Services\PortfolioService;

class InvestClient
{
    private HttpClientInterface $httpClient;
    private array $services = [];

    public function __construct(Config $config)
    {
        $baseClient = new HttpClient(
            $config->getApiUrl(),
            $config->getApiToken(),
            $config->getApiTimeout()
        );

        $this->httpClient = $config->isLoggingEnabled()
            ? new LoggableHttpClient($baseClient, new Logger($config, $config->isLoggingFull()))
            : $baseClient;
    }

    public function accounts(): AccountService
    {
        return $this->services['accounts'] ??= ServiceFactory::createAccountService($this->httpClient);
    }

    public function portfolio(): PortfolioService
    {
        return $this->services['portfolio'] ??= ServiceFactory::createPortfolioService($this->httpClient);
    }

    public function operations(): OperationsService
    {
        return $this->services['operations'] ??= ServiceFactory::createOperationsService($this->httpClient);
    }

    public function bonds(): BondsService
    {
        return $this->services['bonds'] ??= ServiceFactory::createBondsService($this->httpClient);
    }
}
