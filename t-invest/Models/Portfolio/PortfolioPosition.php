<?php

namespace Tinkoff\Invest\Models\Portfolio;

/**
 * Позиции в портфеле
 * @see https://russianinvestments.github.io/investAPI/operations/#portfolioposition
 */
class PortfolioPosition
{
    /**
     * @param string $figi FIGI-идентификатор инструмента
     * @param string $instrumentType Тип инструмента (stock, bond, etf, currency, future)
     * @param int $quantity Количество инструмента в портфеле в штуках
     * @param object $averagePositionPrice Средневзвешенная цена позиции (без НКД)
     * @param string|null $positionUid Уникальный идентификатор позиции
     * @param string|null $instrumentUid Уникальный идентификатор инструмента
     * @param int|null $quantityLots Количество лотов в портфеле
     * @param object|null $currentPrice Текущая рассчитанная цена инструмента
     * @param object|null $averagePositionPriceFifo Средняя цена позиции по методу FIFO (без НКД)
     * @param object|null $averagePositionPricePt Средняя цена позиции по методу Pt (без НКД)
     * @param object|null $expectedYield Текущая рассчитанная доходность позиции (без НКД)
     * @param object|null $expectedYieldFifo Текущая рассчитанная доходность позиции по методу FIFO (без НКД)
     * @param object|null $dailyYield Рассчитанная доходность портфеля за день
     * @param object|null $currentNkd Текущий НКД (для облигаций)
     * @param object|null $varMargin Вариационная маржа (для фьючерсов)
     * @param bool|null $blocked Признак блокировки позиции
     * @param int|null $blockedLots Количество заблокированных лотов
     */
    public function __construct(
        public string $figi,
        public string $instrumentType,
        public int $quantity,
        public object $averagePositionPrice,
        public ?string $positionUid = null,
        public ?string $instrumentUid = null,
        public ?int $quantityLots = null,
        public ?object $currentPrice = null,
        public ?object $averagePositionPriceFifo = null,
        public ?object $averagePositionPricePt = null,
        public ?object $expectedYield = null,
        public ?object $expectedYieldFifo = null,
        public ?object $dailyYield = null,
        public ?object $currentNkd = null,
        public ?object $varMargin = null,
        public ?bool $blocked = null,
        public ?int $blockedLots = null
    ) {
    }
}
