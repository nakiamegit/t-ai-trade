<?php

namespace Tinkoff\Invest\Models\Operations;

/**
 * Список операций с пагинацией.
 * @see https://russianinvestments.github.io/investAPI/operations/#operationsresponse
 */
class OperationsResponse
{
    /**
     * @param array<Operation> $operations Список операций
     * @param string|null $nextCursor Указатель на следующую страницу
     */
    public function __construct(
        public array $operations,
        public ?string $nextCursor = null
    ) {
    }
}
