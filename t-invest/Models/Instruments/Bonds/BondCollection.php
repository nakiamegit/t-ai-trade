<?php

namespace Tinkoff\Invest\Models\Instruments\Bonds;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * Коллекция облигаций.
 *
 * @implements IteratorAggregate<Bond>
 */
final class BondCollection implements IteratorAggregate
{
    /**
     * @param Bond[] $bonds Массив облигаций
     */
    public function __construct(
        private array $bonds = []
    ) {
    }

    /**
     * Возвращает итератор для коллекции.
     *
     * @return Traversable<Bond>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->bonds);
    }

    /**
     * Возвращает первую облигацию в коллекции.
     */
    public function first(): ?Bond
    {
        return $this->bonds[0] ?? null;
    }

    /**
     * Возвращает количество облигаций в коллекции.
     */
    public function count(): int
    {
        return count($this->bonds);
    }

    /**
     * Проверяет, пуста ли коллекция.
     */
    public function isEmpty(): bool
    {
        return empty($this->bonds);
    }
}
