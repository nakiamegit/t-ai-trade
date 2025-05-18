<?php

namespace Tinkoff\Invest;

use Tinkoff\Invest\Exceptions\ClientException;
use Tinkoff\Invest\Transport\HttpClient;
use Tinkoff\Invest\Transport\HttpClientInterface;
use Tinkoff\Invest\Transport\LoggableHttpClient;

class InvestClient
{
    private HttpClientInterface $httpClient;
    private array $services = [];

    public function __construct(Config $config)
    {
        try {
            $baseClient = new HttpClient(
                $config->getApiUrl(),
                $config->getApiToken(),
                $config->getApiTimeout()
            );

            $this->httpClient = $config->isLoggingEnabled()
                ? new LoggableHttpClient(
                    $baseClient,
                    new Logger($config)
                )
                : $baseClient;

        } catch (\Throwable $e) {
            throw ClientException::invalidClientState(
                "HTTP client initialization failed: " . $e->getMessage(),
                [
                    'config' => [
                        'logging_enabled' => $config->isLoggingEnabled(),
                        'log_sensitive_data' => $config->isLoggingSensitiveData(),
                        'full_logging' => $config->isLoggingFull(),
                        'api_url' => $config->getApiUrl(),
                        'timeout' => $config->getApiTimeout()
                    ],
                    'exception' => ClientException::buildExceptionContext($e)
                ],
                $e
            );
        }
    }

    public function accounts(): Services\AccountService
    {
        try {
            return $this->services['accounts'] ??= Services\ServiceFactory::createAccountService($this->httpClient);
        } catch (\Throwable $e) {
            throw ClientException::serviceNotAvailable(
                'AccountService',
                $e,
                ['service_method' => 'accounts']
            );
        }
    }

    public function portfolio(): Services\PortfolioService
    {
        try {
            return $this->services['portfolio'] ??= Services\ServiceFactory::createPortfolioService($this->httpClient);
        } catch (\Throwable $e) {
            throw ClientException::serviceNotAvailable(
                'PortfolioService',
                $e,
                ['service_method' => 'portfolio']
            );
        }
    }

    public function operations(): Services\OperationsService
    {
        try {
            return $this->services['operations'] ??= Services\ServiceFactory::createOperationsService($this->httpClient);
        } catch (\Throwable $e) {
            throw ClientException::serviceNotAvailable(
                'OperationsService',
                $e,
                ['service_method' => 'operations']
            );
        }
    }

    public function bonds(): Services\BondsService
    {
        try {
            return $this->services['bonds'] ??= Services\ServiceFactory::createBondsService($this->httpClient);
        } catch (\Throwable $e) {
            throw ClientException::serviceNotAvailable(
                'BondsService',
                $e,
                ['service_method' => 'bonds']
            );
        }
    }
}