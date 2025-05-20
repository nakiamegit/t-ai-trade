<?php

namespace Tinkoff\Invest\Models\Instruments\Bonds;

use DateTimeInterface;
use Tinkoff\Invest\Models\DataTypes\MoneyValue;
use Tinkoff\Invest\Models\DataTypes\Quotation;

/**
 * Накопленный купонный доход (НКД) по облигации.
 * @see https://tinkoff.github.io/investAPI/instruments/#accruedinterest
 */
final class AccruedInterest
{
    /**
     * @param DateTimeInterface $date Дата
     * @param Quotation $value Значение НКД
     * @param MoneyValue $valuePercent Значение НКД в процентах
     * @param MoneyValue $nominal Номинал облигации
     */
    public function __construct(
        public DateTimeInterface $date,
        public Quotation $value,
        public MoneyValue $valuePercent,
        public MoneyValue $nominal
    ) {
    }
}
