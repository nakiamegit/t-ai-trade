<?php

namespace Tinkoff\Invest\Models\Enums;

/**
 * Типы операций
 * @see https://russianinvestments.github.io/investAPI/operations/#operationtype
 */
final class OperationType
{
    public const UNSPECIFIED = 'OPERATION_TYPE_UNSPECIFIED';
    public const INPUT = 'OPERATION_TYPE_INPUT';
    public const BOND_TAX = 'OPERATION_TYPE_BOND_TAX';
    public const OUTPUT_SECURITIES = 'OPERATION_TYPE_OUTPUT_SECURITIES';
    public const OTC_TAX = 'OPERATION_TYPE_OTC_TAX';
    public const DIVIDEND = 'OPERATION_TYPE_DIVIDEND';
    public const DIVIDEND_TAX = 'OPERATION_TYPE_DIVIDEND_TAX';
    public const INPUT_SECURITIES = 'OPERATION_TYPE_INPUT_SECURITIES';
    public const SELL = 'OPERATION_TYPE_SELL';
    public const BUY = 'OPERATION_TYPE_BUY';
    public const BROKER_FEE = 'OPERATION_TYPE_BROKER_FEE';
    public const MARKET_FEE = 'OPERATION_TYPE_MARKET_FEE';
    public const OTHER_FEE = 'OPERATION_TYPE_OTHER_FEE';
    public const SERVICE_FEE = 'OPERATION_TYPE_SERVICE_FEE';
    public const MARGIN_FEE = 'OPERATION_TYPE_MARGIN_FEE';
    public const TAX = 'OPERATION_TYPE_TAX';
    public const TAX_LUCRE = 'OPERATION_TYPE_TAX_LUCRE';
    public const TAX_DIVIDEND = 'OPERATION_TYPE_TAX_DIVIDEND';
    public const TAX_COUPON = 'OPERATION_TYPE_TAX_COUPON';
    public const TAX_BACK = 'OPERATION_TYPE_TAX_BACK';
    public const REPAYMENT = 'OPERATION_TYPE_REPAYMENT';
    public const PART_REPAYMENT = 'OPERATION_TYPE_PART_REPAYMENT';
    public const COUPON = 'OPERATION_TYPE_COUPON';
    public const RESULT = 'OPERATION_TYPE_RESULT';
    public const TAX_CORRECTION = 'OPERATION_TYPE_TAX_CORRECTION';
    public const SERVICE_FEE_RETURN = 'OPERATION_TYPE_SERVICE_FEE_RETURN';

    public const DESCRIPTIONS = [
        self::UNSPECIFIED => 'Не указан',
        self::INPUT => 'Пополнение брокерского счёта',
        self::BOND_TAX => 'Удержание налога по купонам',
        self::OUTPUT_SECURITIES => 'Вывод ценных бумаг',
        self::OTC_TAX => 'Удержание налога по внебиржевым сделкам',
        self::DIVIDEND => 'Выплата дивидендов',
        self::DIVIDEND_TAX => 'Удержание налога по дивидендам',
        self::INPUT_SECURITIES => 'Ввод ценных бумаг',
        self::SELL => 'Продажа',
        self::BUY => 'Покупка',
        self::BROKER_FEE => 'Комиссия брокера',
        self::MARKET_FEE => 'Комиссия биржи',
        self::OTHER_FEE => 'Иные комиссии',
        self::SERVICE_FEE => 'Комиссия за обслуживание',
        self::MARGIN_FEE => 'Комиссия за маржинальное обслуживание',
        self::TAX => 'Налог',
        self::TAX_LUCRE => 'Налог на прибыль',
        self::TAX_DIVIDEND => 'Налог на дивиденды',
        self::TAX_COUPON => 'Налог на купоны',
        self::TAX_BACK => 'Возврат налога',
        self::REPAYMENT => 'Погашение облигации',
        self::PART_REPAYMENT => 'Частичное погашение',
        self::COUPON => 'Выплата купона',
        self::RESULT => 'Результат сделки',
        self::TAX_CORRECTION => 'Корректировка налога',
        self::SERVICE_FEE_RETURN => 'Возврат комиссии'
    ];

    public static function getDescription(string $type): string
    {
        return self::DESCRIPTIONS[$type] ?? 'Неизвестный тип операции (' . $type . ')';
    }

//    public static function getAllTypes(): array
//    {
//        return self::DESCRIPTIONS;
//    }
}
