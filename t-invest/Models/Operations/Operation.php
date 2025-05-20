<?php

namespace Tinkoff\Invest\Models\Operations;

use Tinkoff\Invest\Models\DataTypes\MoneyValue;
use Tinkoff\Invest\Models\DataTypes\Quotation;

/**
 * Информация об операции.
 * @see https://russianinvestments.github.io/investAPI/operations/#operation
 */
class Operation
{
    /**
     * @param string $id Идентификатор операции
     * @param string $parentOperationId Идентификатор родительской операции
     * @param string $currency Валюта операции
     * @param MoneyValue $payment Сумма операции
     * @param MoneyValue|null $price Цена за 1 инструмент
     * @param string $state Статус операции
     * @param string $quantity Количество инструмента
     * @param string $quantityRest Неисполненный остаток
     * @param string $figi FIGI инструмента
     * @param string|null $instrumentType Тип инструмента
     * @param \DateTimeInterface $date Дата и время операции
     * @param string $type Тип операции
     * @param Quotation|null $commission Комиссия
     * @param string|null $ticker Тикер инструмента
     * @param string|null $tradeId Идентификатор сделки
     * @param string|null $orderId Идентификатор заявки
     * @param string|null $instrumentUid Уникальный идентификатор инструмента
     * @param string|null $positionUid Уникальный идентификатор позиции
     * @param string|null $operationType Тип операции (для брокерского счета)
     * @param array<Trade> $trades Список сделок
     * @param array<Operation> $childOperations Дочерние операции
     */
    public function __construct(
        public string $id,
        public string $parentOperationId,
        public string $currency,
        public MoneyValue $payment,
        public ?MoneyValue $price,
        public string $state,
        public string $quantity,
        public string $quantityRest,
        public string $figi,
        public ?string $instrumentType,
        public \DateTimeInterface $date,
        public string $type,
        public ?Quotation $commission,
        public ?string $ticker,
        public ?string $tradeId,
        public ?string $orderId,
        public ?string $instrumentUid,
        public ?string $positionUid,
        public ?string $operationType,
        public array $trades = [],
        public array $childOperations = []
    ) {
    }

    public function getChildOperations(): array
    {
        return $this->childOperations;
    }
}
