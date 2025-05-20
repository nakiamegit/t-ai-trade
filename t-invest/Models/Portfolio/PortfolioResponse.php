<?php

namespace Tinkoff\Invest\Models\Portfolio;

/**
 * Информация о портфеле
 * @see https://russianinvestments.github.io/investAPI/operations/#portfolioresponse
 */
class PortfolioResponse
{
    /**
     * @param string $accountId Идентификатор счёта
     * @param object $totalAmount Общая стоимость портфеля
     * @param object $totalShares Стоимость акций
     * @param object $totalBonds Стоимость облигаций
     * @param object $totalEtf Стоимость ETF
     * @param object $totalCurrencies Стоимость валют
     * @param object $totalFutures Стоимость фьючерсов
     * @param object $totalOptions Стоимость опционов
     * @param object $totalSp Стоимость структурных нот
     * @param object $expectedYield Текущая относительная доходность
     * @param object $dailyYield Доходность за день в руб.
     * @param object $dailyYieldRelative Доходность за день в %
     * @param array $positions Массив позиций
     * @param array $virtualPositions Массив виртуальных позиций
     */
    public function __construct(
        public string $accountId,
        public object $totalAmount,
        public object $totalShares,
        public object $totalBonds,
        public object $totalEtf,
        public object $totalCurrencies,
        public object $totalFutures,
        public object $totalOptions,
        public object $totalSp,
        public object $expectedYield,
        public object $dailyYield,
        public object $dailyYieldRelative,
        public array $positions,
        public array $virtualPositions = []
    ) {
    }
}
