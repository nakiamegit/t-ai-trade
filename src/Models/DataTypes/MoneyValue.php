<?php

namespace Tinkoff\Invest\Models\DataTypes;

final readonly class MoneyValue
{
    use UnitsAndNanoConverter;

    public function __construct(
        public string $value, // Десятичная строка, например "123.45"
        public string $currency // "RUB", "USD" и т.д.
    ) {
    }

    /**
     * Создает MoneyValue из массива API (units, nano, currency).
     */
    public static function fromApi(array $data): self
    {
        $units = $data['units'] ?? 0;
        $nano = $data['nano'] ?? 0;
        $currency = $data['currency'] ?? 'RUB';

        $value = self::unitsAndNanoToDecimal($units, $nano);
        return new self($value, $currency);
    }
}
