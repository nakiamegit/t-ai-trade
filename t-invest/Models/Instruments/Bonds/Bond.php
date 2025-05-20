<?php

namespace Tinkoff\Invest\Models\Instruments\Bonds;

use DateTimeInterface;
use Tinkoff\Invest\Models\DataTypes\MoneyValue;
use Tinkoff\Invest\Models\DataTypes\Quotation;
use Tinkoff\Invest\Models\Enums\RealExchange;
use Tinkoff\Invest\Models\Enums\RiskLevel;
use Tinkoff\Invest\Models\Enums\SecurityTradingStatus;

/**
 * Данные об облигации.
 *
 * @see https://tinkoff.github.io/investAPI/instruments/#bond
 */
final class Bond
{
    /**
     * @param string $figi Figi-идентификатор инструмента
     * @param string $ticker Тикер инструмента
     * @param string $classCode Класс-код (секция торгов)
     * @param string $isin Isin-идентификатор инструмента
     * @param int $lot Лотность инструмента
     * @param string $currency Валюта расчётов
     * @param Quotation $klong Коэффициент ставки риска длинной позиции
     * @param Quotation $kshort Коэффициент ставки риска короткой позиции
     * @param Quotation $dlong Ставка риска начальной маржи для КСУР лонг
     * @param Quotation $dshort Ставка риска начальной маржи для КСУР шорт
     * @param Quotation $dlongMin Ставка риска начальной маржи для КПУР лонг
     * @param Quotation $dshortMin Ставка риска начальной маржи для КПУР шорт
     * @param bool $shortEnabledFlag Признак доступности для операций в шорт
     * @param string $name Название инструмента
     * @param string $exchange Торговая площадка
     * @param int $couponQuantityPerYear Количество выплат по купонам в год
     * @param Quotation $minPriceIncrement Шаг цены
     * @param bool $apiTradeAvailableFlag Доступность торговли через API
     * @param RealExchange $realExchange Реальная площадка исполнения расчётов
     * @param DateTimeInterface|null $maturityDate Дата погашения
     * @param MoneyValue|null $nominal Номинал облигации
     * @param MoneyValue|null $initialNominal Первоначальный номинал
     * @param DateTimeInterface|null $stateRegDate Дата выпуска облигации
     * @param DateTimeInterface|null $placementDate Дата размещения
     * @param MoneyValue|null $placementPrice Цена размещения
     * @param MoneyValue|null $aciValue Значение НКД на дату
     * @param string|null $countryOfRisk Код страны риска
     * @param string|null $countryOfRiskName Название страны риска
     * @param string|null $sector Сектор экономики
     * @param string|null $issueKind Форма выпуска (documentary/non_documentary)
     * @param int|null $issueSize Размер выпуска
     * @param int|null $issueSizePlan Плановый размер выпуска
     * @param SecurityTradingStatus|null $tradingStatus Текущий режим торгов
     * @param bool $otcFlag Признак внебиржевой ценной бумаги
     * @param bool $buyAvailableFlag Признак доступности для покупки
     * @param bool $sellAvailableFlag Признак доступности для продажи
     * @param bool $floatingCouponFlag Признак плавающего купона
     * @param bool $perpetualFlag Признак бессрочной облигации
     * @param bool $amortizationFlag Признак амортизации долга
     * @param string|null $uid Уникальный идентификатор инструмента
     * @param string|null $positionUid Уникальный идентификатор позиции
     * @param bool $forIisFlag Доступность для ИИС
     * @param bool $forQualInvestorFlag Только для квалифицированных инвесторов
     * @param bool $weekendFlag Доступность торговли по выходным
     * @param bool $blockedTcaFlag Флаг заблокированного ТКС
     * @param bool $subordinatedFlag Признак субординированной облигации
     * @param bool $liquidityFlag Флаг ликвидности
     * @param DateTimeInterface|null $first1minCandleDate Дата первой минутной свечи
     * @param DateTimeInterface|null $first1dayCandleDate Дата первой дневной свечи
     * @param RiskLevel|null $riskLevel Уровень риска
     */
    public function __construct(
        public string $figi,
        public string $ticker,
        public string $classCode,
        public string $isin,
        public int $lot,
        public string $currency,
        public Quotation $klong,
        public Quotation $kshort,
        public Quotation $dlong,
        public Quotation $dshort,
        public Quotation $dlongMin,
        public Quotation $dshortMin,
        public bool $shortEnabledFlag,
        public string $name,
        public string $exchange,
        public int $couponQuantityPerYear,
        public Quotation $minPriceIncrement,
        public bool $apiTradeAvailableFlag,
        public RealExchange $realExchange,
        public ?DateTimeInterface $maturityDate = null,
        public ?MoneyValue $nominal = null,
        public ?MoneyValue $initialNominal = null,
        public ?DateTimeInterface $stateRegDate = null,
        public ?DateTimeInterface $placementDate = null,
        public ?MoneyValue $placementPrice = null,
        public ?MoneyValue $aciValue = null,
        public ?string $countryOfRisk = null,
        public ?string $countryOfRiskName = null,
        public ?string $sector = null,
        public ?string $issueKind = null,
        public ?int $issueSize = null,
        public ?int $issueSizePlan = null,
        public ?SecurityTradingStatus $tradingStatus = null,
        public bool $otcFlag = false,
        public bool $buyAvailableFlag = false,
        public bool $sellAvailableFlag = false,
        public bool $floatingCouponFlag = false,
        public bool $perpetualFlag = false,
        public bool $amortizationFlag = false,
        public ?string $uid = null,
        public ?string $positionUid = null,
        public bool $forIisFlag = false,
        public bool $forQualInvestorFlag = false,
        public bool $weekendFlag = false,
        public bool $blockedTcaFlag = false,
        public bool $subordinatedFlag = false,
        public bool $liquidityFlag = false,
        public ?DateTimeInterface $first1minCandleDate = null,
        public ?DateTimeInterface $first1dayCandleDate = null,
        public ?RiskLevel $riskLevel = null
    ) {
    }
}
