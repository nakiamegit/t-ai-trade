<?php

namespace Tinkoff\Invest\Services;

use Tinkoff\Invest\Exceptions\ApiException;
use Tinkoff\Invest\Models\Accounts\Account;
use Tinkoff\Invest\Models\Accounts\AccountCollection;
use Tinkoff\Invest\Models\Enums\AccountStatus;
use Tinkoff\Invest\Models\Enums\AccountType;
use Tinkoff\Invest\Transport\HttpClient;

class AccountService
{
    public function __construct(private HttpClient $httpClient)
    {
    }

    public function getAccounts(): AccountCollection
    {
        $response = $this->httpClient->request(
            'POST',
            'tinkoff.public.invest.api.contract.v1.UsersService/GetAccounts'
        );

        if (!isset($response['accounts'])) {
            throw new ApiException('Invalid accounts response');
        }

        $accounts = array_map(
        /**
         * @throws \Exception
         */
            static function (array $data) {
                return new Account(
                    id: $data['id'],
                    type: AccountType::fromApi($data['type']),
                    name: $data['name'],
                    status: AccountStatus::fromApi($data['status']),
                    openedDate: new \DateTimeImmutable($data['openedDate'])
                );
            },
            $response['accounts']
        );

        return new AccountCollection($accounts);
    }
}
