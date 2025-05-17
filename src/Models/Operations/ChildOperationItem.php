<?php

namespace Tinkoff\Invest\Models\Operations;

use Tinkoff\Invest\Models\DataTypes\MoneyValue;
use Tinkoff\Invest\Models\DataTypes\Quotation;

/**
 * Дочерняя операция.
 * @see https://developer.tbank.ru/invest/services/operations/methods#childoperationitem
 */
class ChildOperationItem
{
    /**
     * @param string $instrumentUid Уникальный идентификатор инструмента
     * @param MoneyValue $payment Сумма операции
     * @param string|null $parentOperationId Идентификатор родительской операции
     * @param string|null $currency Валюта операции
     * @param Quotation|null $commission Комиссия
     * @param string|null $operationType Тип операции
     * @param string|null $type Тип операции
     * @param string|null $state Статус операции
     */
    public function __construct(
        private string $instrumentUid,
        private MoneyValue $payment,
        private ?string $parentOperationId = null,
        private ?string $currency = null,
        private ?Quotation $commission = null,
        private ?string $operationType = null,
        private ?string $type = null,
        private ?string $state = null
    ) {
    }

    public function getInstrumentUid(): string
    {
        return $this->instrumentUid;
    }

    public function getPayment(): MoneyValue
    {
        return $this->payment;
    }

    public function getParentOperationId(): ?string
    {
        return $this->parentOperationId;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getCommission(): ?Quotation
    {
        return $this->commission;
    }

    public function getOperationType(): ?string
    {
        return $this->operationType;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getState(): ?string
    {
        return $this->state;
    }
}