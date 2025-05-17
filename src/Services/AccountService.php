<?php

namespace Tinkoff\Invest\Services;

use DateTimeImmutable;
use Tinkoff\Invest\Transport\HttpClientInterface;
use Tinkoff\Invest\Models\Accounts\Account;
use Tinkoff\Invest\Models\Accounts\AccountCollection;
use Tinkoff\Invest\Models\Enums\AccountStatus;
use Tinkoff\Invest\Models\Enums\AccountType;
use Tinkoff\Invest\Exceptions\Services\AccountServiceException;

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
                $accounts[] = $this->transformAccountData($accountData);
            }

            return new AccountCollection($accounts);

        } catch (\Throwable $e) {
            throw AccountServiceException::serviceUnavailable('GetAccounts', $e);
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
            throw AccountServiceException::serviceUnavailable('GetMarginAttributes', $e);
        }
    }

    private function transformAccountData(array $data): Account
    {
        $requiredFields = ['id', 'type', 'name', 'status', 'openedDate'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw AccountServiceException::invalidAccountResponse($data);
            }
        }

        try {
            return new Account(
                $data['id'],
                AccountType::fromApi($data['type']),
                $data['name'],
                AccountStatus::fromApi($data['status']),
                new DateTimeImmutable($data['openedDate'])
            );
        } catch (\Exception $e) {
            throw AccountServiceException::invalidAccountResponse($data, $e);
        }
    }
}