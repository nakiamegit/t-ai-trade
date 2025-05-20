<?php

namespace Tinkoff\Invest\Models\Instruments\Bonds;

class BondCoupon
{
    public function __construct(
        public string $figi,
        public \DateTimeInterface $couponDate,
        public int $couponNumber,
        public \DateTimeInterface $fixDate,
        public float $payOneBond,
        public string $couponType,
        public \DateTimeInterface $couponStartDate,
        public \DateTimeInterface $couponEndDate,
        public int $couponPeriod,
        public string $currency
    ) {
    }
}
