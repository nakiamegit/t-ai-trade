<?php

namespace Tinkoff\Invest\Services;

use Tinkoff\Invest\Exceptions\Services\AccountServiceException;
use Tinkoff\Invest\Models\Enums\AccountStatus;
use Tinkoff\Invest\Models\Enums\AccountType;
use Tinkoff\Invest\Transport\HttpClientInterface;
use Tinkoff\Invest\Models\Accounts\Account;
use Tinkoff\Invest\Models\Accounts\AccountCollection;

class AccountService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getAccounts(): AccountCollection
    {
        try {
            $response = $this->httpClient->post(
                'tinkoff.public.invest.api.contract.v1.UsersService/GetAccounts'
            );

            if (!isset($response['accounts'])) {
                throw AccountServiceException::invalidAccountResponse($response);
            }

            $accounts = [];
            foreach ($response['accounts'] as $accountData) {
                try {
                    $accounts[] = $this->transformAccountData($accountData);
                } catch (\Throwable $e) {
                    throw AccountServiceException::invalidAccountResponse(
                        $accountData,
                        $e
                    );
                }
            }

            return new AccountCollection($accounts);

        } catch (AccountServiceException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw AccountServiceException::invalidAccountResponse(
                $response ?? [],
                $e
            );
        }
    }

    public function getMarginAttributes(string $accountId): array
    {
        try {
            $response = $this->httpClient->post(
                'tinkoff.public.invest.api.contract.v1.UsersService/GetMarginAttributes',
                ['accountId' => $accountId]
            );

            if (!isset($response['liquidPortfolio'])) {
                throw AccountServiceException::marginAttributesNotFound($accountId);
            }

            return $response;

        } catch (AccountServiceException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw AccountServiceException::invalidAccountResponse(
                $response ?? [],
                $e
            );
        }
    }

    private function transformAccountData(array $data): Account
    {
        $requiredFields = ['id', 'type', 'name', 'status', 'openedDate'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw AccountServiceException::invalidAccountResponse(
                    $data,
                    new \InvalidArgumentException("Missing required field: {$field}")
                );
            }
        }

        try {
            return new Account(
                $data['id'],
                AccountType::fromApi($data['type']),
                $data['name'],
                AccountStatus::fromApi($data['status']),
                new \DateTimeImmutable($data['openedDate'])
            );
        } catch (\Throwable $e) {
            throw AccountServiceException::invalidAccountResponse($data, $e);
        }
    }
}