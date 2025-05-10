<?php

namespace Tinkoff\Invest\Services;

use Tinkoff\Invest\Models\DataTypes\MoneyValue;
use Tinkoff\Invest\Models\DataTypes\Quotation;
use Tinkoff\Invest\Models\Operations\Operation;
use Tinkoff\Invest\Models\Operations\OperationsResponse;
use Tinkoff\Invest\Models\Operations\Trade;
use Tinkoff\Invest\Transport\HttpClient;
use Tinkoff\Invest\Exceptions\ApiException;

/**
 * Сервис работы с операциями.
 * @see https://russianinvestments.github.io/investAPI/operations/
 */
class OperationsService
{
    public function __construct(private HttpClient $httpClient)
    {
    }

    /**
     * Получить список операций по счёту
     * @param string $accountId Идентификатор счёта
     * @param \DateTimeInterface $from Начало периода
     * @param \DateTimeInterface $to Конец периода
     * @param string $state Статус операций (default: OPERATION_STATE_EXECUTED)
     * @param string $figi FIGI инструмента (optional)
     * @param string|null $cursor Пагинация (optional)
     * @param int $limit Лимит операций (default: 1000, max: 1000)
     * @throws ApiException
     */
    public function getOperations(
        string $accountId,
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        string $state = 'OPERATION_STATE_EXECUTED',
        string $figi = '',
        ?string $cursor = null,
        int $limit = 1000
    ): OperationsResponse {
        $response = $this->httpClient->request(
            'POST',
            'tinkoff.public.invest.api.contract.v1.OperationsService/GetOperations',
            [
                'accountId' => $accountId,
                'from' => $from->format(\DateTimeInterface::ATOM),
                'to' => $to->format(\DateTimeInterface::ATOM),
                'state' => $state,
                'figi' => $figi,
                'cursor' => $cursor,
                'limit' => $limit
            ]
        );

        return $this->transformResponse($response);
    }

    private function transformResponse(array $response): OperationsResponse
    {
        if (!isset($response['operations']) || !is_array($response['operations'])) {
            throw new ApiException('Invalid response: operations field missing or invalid');
        }

        return new OperationsResponse(
            operations: array_filter(array_map(
                [$this, 'transformOperation'],
                $response['operations']
            )),
            nextCursor: $response['nextCursor'] ?? null
        );
    }

    private function transformOperation(array $data): ?Operation
    {
        try {
            // Обязательные поля
            if (!isset($data['id'], $data['state'], $data['date'], $data['type'], $data['payment'])) {
                throw new ApiException('Missing required operation fields');
            }

            return new Operation(
                id: $data['id'],
                parentOperationId: $data['parentOperationId'] ?? '',
                currency: $data['currency'] ?? 'RUB',
                payment: MoneyValue::fromApi($data['payment']),
                price: isset($data['price']) ? MoneyValue::fromApi($data['price']) : null,
                state: $data['state'],
                quantity: $data['quantity'] ?? '0',
                quantityRest: $data['quantityRest'] ?? '0',
                figi: $data['figi'] ?? '',
                instrumentType: $data['instrumentType'] ?? null,
                date: new \DateTime($data['date']),
                type: $data['type'],
                commission: isset($data['commission']) ? Quotation::fromApi($data['commission']) : null,
                ticker: $data['ticker'] ?? null,
                tradeId: $data['tradeId'] ?? null,
                orderId: $data['orderId'] ?? null,
                instrumentUid: $data['instrumentUid'] ?? null,
                positionUid: $data['positionUid'] ?? null,
                operationType: $data['operationType'] ?? null,
                trades: isset($data['trades']) ? $this->transformTrades($data['trades']) : [],
                childOperations: isset($data['childOperations'])
                    ? array_filter(array_map(
                        [$this, 'transformOperation'],
                        $data['childOperations']
                    ))
                    : []
            );
        } catch (\Exception $e) {
            // Логируем ошибку, но продолжаем обработку других операций
            error_log('Failed to transform operation: ' . $e->getMessage());
            return null;
        }
    }

    private function transformTrades(array $tradesData): array
    {
        return array_filter(array_map(
            static function (array $tradeData) {
                try {
                    if (!isset($tradeData['tradeId'], $tradeData['dateTime'], $tradeData['quantity'], $tradeData['price'])) {
                        throw new ApiException('Missing required trade fields');
                    }

                    return new Trade(
                        tradeId: $tradeData['tradeId'],
                        dateTime: new \DateTime($tradeData['dateTime']),
                        quantity: $tradeData['quantity'],
                        price: MoneyValue::fromApi($tradeData['price']),
                        orderId: $tradeData['orderId'] ?? null
                    );
                } catch (\Exception $e) {
                    error_log('Failed to transform trade: ' . $e->getMessage());
                    return null;
                }
            },
            $tradesData
        ));
    }
}
