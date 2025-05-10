<?php

namespace Tinkoff\Invest\Models\Accounts;

use ArrayIterator;
use IteratorAggregate;

class AccountCollection implements IteratorAggregate
{
    /** @var Account[] */
    private array $accounts = [];

    public function __construct(array $accounts)
    {
        foreach ($accounts as $account) {
            $this->add($account);
        }
    }

    public function add(Account $account): void
    {
        $this->accounts[] = $account;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->accounts);
    }

    public function first(): ?Account
    {
        return $this->accounts[0] ?? null;
    }

    public function count(): int
    {
        return count($this->accounts);
    }
}
