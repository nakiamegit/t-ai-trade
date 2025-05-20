<?php

namespace Rest\Services;

use Rest\Transformers\PortfolioTransformer;

class PortfolioService extends BaseService
{
    public function getPortfolio(string $accountId): object
    {
        return $this->client->portfolio()->getFullPortfolio($accountId);
    }

    public function getFormattedPortfolio(string $accountId): array
    {
        $portfolio = $this->getPortfolio($accountId);

        return [
            'totalValue' => $portfolio->totalAmount,
            'positions' => array_map(
                [PortfolioTransformer::class, 'transform'],
                iterator_to_array($portfolio->positions)
            )
        ];
    }
}
