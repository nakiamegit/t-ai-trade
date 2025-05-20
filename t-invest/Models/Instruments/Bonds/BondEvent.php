<?php

namespace Tinkoff\Invest\Models\Instruments\Bonds;

use DateTimeInterface;
use Tinkoff\Invest\Models\DataTypes\MoneyValue;
use Tinkoff\Invest\Models\DataTypes\Quotation;
use Tinkoff\Invest\Models\Enums\BondEventType;

/**
 * Событие по облигации.
 * @see https://developer.tbank.ru/invest/services/instruments/methods#getbondeventsresponsebondevent
 */
final class BondEvent
{
    /**
     * @param string $instrumentId Идентификатор инструмента
     * @param int $eventNumber Номер события для данного типа
     * @param DateTimeInterface $eventDate Дата события
     * @param BondEventType $eventType Тип события
     * @param Quotation $eventTotalVol Полное количество бумаг
     * @param DateTimeInterface|null $fixDate Дата фиксации владельцев
     * @param DateTimeInterface|null $rateDate Дата определения даты события
     * @param DateTimeInterface|null $defaultDate Дата дефолта
     * @param DateTimeInterface|null $realPayDate Дата реального исполнения
     * @param DateTimeInterface|null $payDate Дата выплаты
     * @param MoneyValue|null $payOneBond Выплата на одну облигацию
     * @param MoneyValue|null $moneyFlowVal Выплаты на все бумаги
     * @param string|null $execution Признак исполнения
     * @param string|null $operationType Тип операции
     * @param Quotation|null $value Стоимость операции
     * @param string|null $note Примечание
     * @param string|null $convertToFinToolId ID выпуска для конвертации
     * @param DateTimeInterface|null $couponStartDate Начало купонного периода
     * @param DateTimeInterface|null $couponEndDate Окончание купонного периода
     * @param int|null $couponPeriod Купонный период
     * @param Quotation|null $couponInterestRate Ставка купона
     */
    public function __construct(
        public string $instrumentId,
        public int $eventNumber,
        public DateTimeInterface $eventDate,
        public BondEventType $eventType,
        public Quotation $eventTotalVol,
        public ?DateTimeInterface $fixDate = null,
        public ?DateTimeInterface $rateDate = null,
        public ?DateTimeInterface $defaultDate = null,
        public ?DateTimeInterface $realPayDate = null,
        public ?DateTimeInterface $payDate = null,
        public ?MoneyValue $payOneBond = null,
        public ?MoneyValue $moneyFlowVal = null,
        public ?string $execution = null,
        public ?string $operationType = null,
        public ?Quotation $value = null,
        public ?string $note = null,
        public ?string $convertToFinToolId = null,
        public ?DateTimeInterface $couponStartDate = null,
        public ?DateTimeInterface $couponEndDate = null,
        public ?int $couponPeriod = null,
        public ?Quotation $couponInterestRate = null,
    ) {
    }
}
