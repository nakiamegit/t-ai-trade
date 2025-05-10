<?php

namespace Tinkoff\Invest\Models\DataTypes;

final readonly class Quotation
{
    use UnitsAndNanoConverter;

    public function __construct(
        public string $value // Десятичная строка, например "123.45"
    ) {
    }

    /**
     * Создает Quotation из массива API (units, nano).
     */
    public static function fromApi(array $data): self
    {
        $units = $data['units'] ?? 0;
        $nano = $data['nano'] ?? 0;

        $value = self::unitsAndNanoToDecimal($units, $nano);
        return new self($value);
    }
}
