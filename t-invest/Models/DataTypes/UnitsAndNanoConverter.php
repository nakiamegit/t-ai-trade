<?php

namespace Tinkoff\Invest\Models\DataTypes;

trait UnitsAndNanoConverter
{
    private static function unitsAndNanoToDecimal(int $units, int $nano): string
    {
        $sign = ($units < 0 || $nano < 0) ? '-' : '';
        $absUnits = abs($units);
        $absNano = abs($nano);

        $nanoStr = str_pad((string)$absNano, 9, '0', STR_PAD_LEFT);
        $nanoStr = rtrim($nanoStr, '0');

        return $nanoStr === ''
            ? $sign . $absUnits
            : $sign . $absUnits . '.' . $nanoStr;
    }
}
