<?php

namespace Rest\Services;

use Rest\Transformers\AccountTransformer;

class AccountService extends BaseService
{
    public function getAllAccounts(): array
    {
        return iterator_to_array($this->client->accounts()->getAccounts());
    }

    public function getFormattedAccounts(): array
    {
        return array_map([AccountTransformer::class, 'transform'], $this->getAllAccounts());
    }
}
