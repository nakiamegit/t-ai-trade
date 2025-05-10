<?php

namespace Tinkoff\Invest\Services;

use Tinkoff\Invest\Models\DataTypes\MoneyValue;
use Tinkoff\Invest\Models\DataTypes\Quotation;
use Tinkoff\Invest\Models\Portfolio\PortfolioPosition;
use Tinkoff\Invest\Models\Portfolio\PortfolioResponse;
use Tinkoff\Invest\Transport\HttpClient;
use Tinkoff\Invest\Exceptions\ApiException;

class PortfolioService
{
    public function __construct(private HttpClient $httpClient) {}

    public function getFullPortfolio(string $accountId, string $currency = 'RUB'): PortfolioResponse
    {
        $response = $this->httpClient->request(
            'POST',
            'tinkoff.public.invest.api.contract.v1.OperationsService/GetPortfolio',
            [
                'accountId' => $accountId,
                'currency' => $currency
            ]
        );

        return $this->transformResponse($response);
    }

    private function transformResponse(array $response): PortfolioResponse
    {
        if (!isset($response['totalAmountPortfolio'])) {
            throw new ApiException('Invalid portfolio response');
        }

        return new PortfolioResponse(
            accountId: $response['accountId'],
            totalAmount: MoneyValue::fromApi($response['totalAmountPortfolio']),
            totalShares: MoneyValue::fromApi($response['totalAmountShares']),
            totalBonds: MoneyValue::fromApi($response['totalAmountBonds']),
            totalEtf: MoneyValue::fromApi($response['totalAmountEtf']),
            totalCurrencies: MoneyValue::fromApi($response['totalAmountCurrencies']),
            totalFutures: MoneyValue::fromApi($response['totalAmountFutures']),
            totalOptions: MoneyValue::fromApi($response['totalAmountOptions']),
            totalSp: MoneyValue::fromApi($response['totalAmountSp']),
            expectedYield: Quotation::fromApi($response['expectedYield']),
            dailyYield: MoneyValue::fromApi($response['dailyYield']),
            dailyYieldRelative: Quotation::fromApi($response['dailyYieldRelative']),
            positions: $this->transformPositions($response['positions'] ?? []),
            virtualPositions: $this->transformPositions($response['virtualPositions'] ?? [])
        );
    }

    private function transformPositions(array $positions): array
    {
        return array_map(
            static fn(array $data) => new PortfolioPosition(
                figi: $data['figi'],
                instrumentType: $data['instrumentType'],
                quantity: (int)($data['quantity']['units'] ?? 0),
                averagePositionPrice: MoneyValue::fromApi($data['averagePositionPrice']),
                positionUid: $data['positionUid'] ?? null,
                instrumentUid: $data['instrumentUid'] ?? null,
                quantityLots: isset($data['quantityLots']) ? (int)$data['quantityLots'] : null,
                currentPrice: isset($data['currentPrice'])
                    ? MoneyValue::fromApi($data['currentPrice'])
                    : null,
                averagePositionPriceFifo: isset($data['averagePositionPriceFifo'])
                    ? MoneyValue::fromApi($data['averagePositionPriceFifo'])
                    : null,
                averagePositionPricePt: isset($data['averagePositionPricePt'])
                    ? MoneyValue::fromApi($data['averagePositionPricePt'])
                    : null,
                expectedYield: isset($data['expectedYield'])
                    ? Quotation::fromApi($data['expectedYield'])
                    : null,
                expectedYieldFifo: isset($data['expectedYieldFifo'])
                    ? Quotation::fromApi($data['expectedYieldFifo'])
                    : null,
                dailyYield: isset($data['dailyYield'])
                    ? Quotation::fromApi($data['dailyYield'])
                    : null,
                currentNkd: isset($data['currentNkd'])
                    ? MoneyValue::fromApi($data['currentNkd'])
                    : null,
                varMargin: isset($data['varMargin'])
                    ? MoneyValue::fromApi($data['varMargin'])
                    : null,
                blocked: $data['blocked'] ?? null,
                blockedLots: isset($data['blockedLots']) ? (int)$data['blockedLots'] : null
            ),
            $positions
        );
    }
}
