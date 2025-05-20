<?php

namespace Tinkoff\Invest\Models\Instruments\Bonds;

use Tinkoff\Invest\Models\DataTypes\MoneyValue;

final class AssetBond
{
    /**
     * @param string $uid Уникальный идентификатор актива
     * @param string $name Название актива
     * @param string $isin ISIN идентификатор
     * @param string $ticker Тикер инструмента
     * @param MoneyValue $currentNominal Текущий номинал
     * @param string|null $borrowName Имя заемщика
     * @param MoneyValue|null $issueSize Объем выпуска
     * @param MoneyValue|null $nominal Номинал
     * @param string|null $issueKind Форма выпуска
     * @param string|null $document Название документа
     * @param string|null $mortgageInfo Ипотечная информация
     * @param string|null $collateralInfo Обеспечение
     * @param string|null $bondType Тип облигации
     * @param string|null $basicSector Сектор экономики
     */
    public function __construct(
        public string $uid,
        public string $name,
        public string $isin,
        public string $ticker,
        public MoneyValue $currentNominal,
        public ?string $borrowName = null,
        public ?MoneyValue $issueSize = null,
        public ?MoneyValue $nominal = null,
        public ?string $issueKind = null,
        public ?string $document = null,
        public ?string $mortgageInfo = null,
        public ?string $collateralInfo = null,
        public ?string $bondType = null,
        public ?string $basicSector = null
    ) {
    }
}
