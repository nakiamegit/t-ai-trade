<?php

namespace Tinkoff\Invest\Models\Operations;

use Tinkoff\Invest\Models\DataTypes\MoneyValue;

/**
 * Информация о сделке.
 * @see https://russianinvestments.github.io/investAPI/operations/#operationtrade
 */
class Trade
{
    /**
     * @param string $tradeId Идентификатор сделки
     * @param \DateTimeInterface $dateTime Дата и время сделки
     * @param string $quantity Количество инструмента
     * @param MoneyValue $price Цена инструмента
     * @param string|null $orderId Идентификатор заявки
     */
    public function __construct(
        public string $tradeId,
        public \DateTimeInterface $dateTime,
        public string $quantity,
        public MoneyValue $price,
        public ?string $orderId = null
    ) {
    }
}