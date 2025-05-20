<?php

namespace Tinkoff\Invest\Models\Accounts;

use DateTimeInterface;
use Tinkoff\Invest\Models\Enums\AccountStatus;
use Tinkoff\Invest\Models\Enums\AccountType;

/**
 * Данные по счёту.
 * @see https://tinkoff.github.io/investAPI/users/#getaccounts
 */
class Account
{
    /**
     * @param string $id Идентификатор счёта
     * @param AccountType $type Тип счёта
     * @param string $name Название счёта
     * @param AccountStatus $status Статус счёта
     * @param DateTimeInterface $openedDate Дата открытия счёта
     */
    public function __construct(
        public string $id,
        public AccountType $type,
        public string $name,
        public AccountStatus $status,
        public DateTimeInterface $openedDate
    ) {
    }
}
