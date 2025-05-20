<?php

namespace Rest\Services;

use Tinkoff\Invest\Models\Operations\OperationsResponse;

class OperationsService extends BaseService
{
    public function getFormattedOperations(OperationsResponse $response): array
    {
        return array_map('\Rest\Transformers\OperationsTransformer::transform', $response->operations ?? []);
    }
}
