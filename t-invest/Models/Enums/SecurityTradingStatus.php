<?php

namespace Tinkoff\Invest\Models\Enums;

enum SecurityTradingStatus: int
{
    case UNSPECIFIED = 0;
    case NOT_AVAILABLE_FOR_TRADING = 1;
    case OPENING_PERIOD = 2;
    case CLOSING_PERIOD = 3;
    case BREAK_IN_TRADING = 4;
    case NORMAL_TRADING = 5;
    case CLOSING_AUCTION = 6;
    case DARK_POOL_AUCTION = 7;
    case DISCRETE_AUCTION = 8;
    case OPENING_AUCTION_PERIOD = 9;
    case TRADING_AT_CLOSING_AUCTION_PRICE = 10;
    case SESSION_ASSIGNED = 11;
    case SESSION_CLOSE = 12;
    case SESSION_OPEN = 13;
    case DEALER_NORMAL_TRADING = 14;
    case DEALER_BREAK_IN_TRADING = 15;
    case DEALER_NOT_AVAILABLE_FOR_TRADING = 16;

    public static function fromApi(string $apiValue): self
    {
        return match ($apiValue) {
            'SECURITY_TRADING_STATUS_NOT_AVAILABLE_FOR_TRADING' => self::NOT_AVAILABLE_FOR_TRADING,
            'SECURITY_TRADING_STATUS_OPENING_PERIOD' => self::OPENING_PERIOD,
            'SECURITY_TRADING_STATUS_CLOSING_PERIOD' => self::CLOSING_PERIOD,
            'SECURITY_TRADING_STATUS_BREAK_IN_TRADING' => self::BREAK_IN_TRADING,
            'SECURITY_TRADING_STATUS_NORMAL_TRADING' => self::NORMAL_TRADING,
            'SECURITY_TRADING_STATUS_CLOSING_AUCTION' => self::CLOSING_AUCTION,
            'SECURITY_TRADING_STATUS_DARK_POOL_AUCTION' => self::DARK_POOL_AUCTION,
            'SECURITY_TRADING_STATUS_DISCRETE_AUCTION' => self::DISCRETE_AUCTION,
            'SECURITY_TRADING_STATUS_OPENING_AUCTION_PERIOD' => self::OPENING_AUCTION_PERIOD,
            'SECURITY_TRADING_STATUS_TRADING_AT_CLOSING_AUCTION_PRICE' => self::TRADING_AT_CLOSING_AUCTION_PRICE,
            'SECURITY_TRADING_STATUS_SESSION_ASSIGNED' => self::SESSION_ASSIGNED,
            'SECURITY_TRADING_STATUS_SESSION_CLOSE' => self::SESSION_CLOSE,
            'SECURITY_TRADING_STATUS_SESSION_OPEN' => self::SESSION_OPEN,
            'SECURITY_TRADING_STATUS_DEALER_NORMAL_TRADING' => self::DEALER_NORMAL_TRADING,
            'SECURITY_TRADING_STATUS_DEALER_BREAK_IN_TRADING' => self::DEALER_BREAK_IN_TRADING,
            'SECURITY_TRADING_STATUS_DEALER_NOT_AVAILABLE_FOR_TRADING' => self::DEALER_NOT_AVAILABLE_FOR_TRADING,
            default => self::UNSPECIFIED,
        };
    }

    public function toApi(): string
    {
        return match ($this) {
            self::NOT_AVAILABLE_FOR_TRADING => 'SECURITY_TRADING_STATUS_NOT_AVAILABLE_FOR_TRADING',
            self::OPENING_PERIOD => 'SECURITY_TRADING_STATUS_OPENING_PERIOD',
            self::CLOSING_PERIOD => 'SECURITY_TRADING_STATUS_CLOSING_PERIOD',
            self::BREAK_IN_TRADING => 'SECURITY_TRADING_STATUS_BREAK_IN_TRADING',
            self::NORMAL_TRADING => 'SECURITY_TRADING_STATUS_NORMAL_TRADING',
            self::CLOSING_AUCTION => 'SECURITY_TRADING_STATUS_CLOSING_AUCTION',
            self::DARK_POOL_AUCTION => 'SECURITY_TRADING_STATUS_DARK_POOL_AUCTION',
            self::DISCRETE_AUCTION => 'SECURITY_TRADING_STATUS_DISCRETE_AUCTION',
            self::OPENING_AUCTION_PERIOD => 'SECURITY_TRADING_STATUS_OPENING_AUCTION_PERIOD',
            self::TRADING_AT_CLOSING_AUCTION_PRICE => 'SECURITY_TRADING_STATUS_TRADING_AT_CLOSING_AUCTION_PRICE',
            self::SESSION_ASSIGNED => 'SECURITY_TRADING_STATUS_SESSION_ASSIGNED',
            self::SESSION_CLOSE => 'SECURITY_TRADING_STATUS_SESSION_CLOSE',
            self::SESSION_OPEN => 'SECURITY_TRADING_STATUS_SESSION_OPEN',
            self::DEALER_NORMAL_TRADING => 'SECURITY_TRADING_STATUS_DEALER_NORMAL_TRADING',
            self::DEALER_BREAK_IN_TRADING => 'SECURITY_TRADING_STATUS_DEALER_BREAK_IN_TRADING',
            self::DEALER_NOT_AVAILABLE_FOR_TRADING => 'SECURITY_TRADING_STATUS_DEALER_NOT_AVAILABLE_FOR_TRADING',
            default => 'SECURITY_TRADING_STATUS_UNSPECIFIED',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::NOT_AVAILABLE_FOR_TRADING => 'Недоступен для торгов',
            self::OPENING_PERIOD => 'Период открытия торгов',
            self::CLOSING_PERIOD => 'Период закрытия торгов',
            self::BREAK_IN_TRADING => 'Перерыв в торговле',
            self::NORMAL_TRADING => 'Нормальная торговля',
            self::CLOSING_AUCTION => 'Аукцион закрытия',
            self::DARK_POOL_AUCTION => 'Аукцион крупных пакетов',
            self::DISCRETE_AUCTION => 'Дискретный аукцион',
            self::OPENING_AUCTION_PERIOD => 'Аукцион открытия',
            self::TRADING_AT_CLOSING_AUCTION_PRICE => 'Период торгов по цене аукциона закрытия',
            self::SESSION_ASSIGNED => 'Сессия назначена',
            self::SESSION_CLOSE => 'Сессия закрыта',
            self::SESSION_OPEN => 'Сессия открыта',
            self::DEALER_NORMAL_TRADING => 'Доступна торговля в режиме внутренней ликвидности брокера',
            self::DEALER_BREAK_IN_TRADING => 'Перерыв торговли в режиме внутренней ликвидности брокера',
            self::DEALER_NOT_AVAILABLE_FOR_TRADING => 'Недоступна торговля в режиме внутренней ликвидности брокера',
            default => 'Торговый статус не определён',
        };
    }
}
