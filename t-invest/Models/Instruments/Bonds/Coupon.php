<?php

namespace Tinkoff\Invest\Models\Instruments\Bonds;

use DateTimeInterface;
use Tinkoff\Invest\Models\DataTypes\MoneyValue;
use Tinkoff\Invest\Models\Enums\CouponType;

/**
 * Данные по купону облигации.
 * @see https://tinkoff.github.io/investAPI/instruments/#coupon
 */
final class Coupon
{
    /**
     * @param string $figi Figi-идентификатор инструмента
     * @param DateTimeInterface $couponDate Дата выплаты купона
     * @param int $couponNumber Номер купона
     * @param DateTimeInterface|null $fixDate Дата фиксации реестра
     * @param MoneyValue $payOneBond Выплата на одну облигацию
     * @param CouponType $couponType Тип купона
     * @param DateTimeInterface $couponStartDate Начало купонного периода
     * @param DateTimeInterface $couponEndDate Окончание купонного периода
     * @param int $couponPeriod Купонный период в днях
     */
    public function __construct(
        public string $figi,
        public DateTimeInterface $couponDate,
        public int $couponNumber,
        public ?DateTimeInterface $fixDate,
        public MoneyValue $payOneBond,
        public CouponType $couponType,
        public DateTimeInterface $couponStartDate,
        public DateTimeInterface $couponEndDate,
        public int $couponPeriod
    ) {
    }
}
