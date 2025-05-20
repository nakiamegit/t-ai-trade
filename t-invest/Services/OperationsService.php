<?php

namespace Tinkoff\Invest\Services;

use Tinkoff\Invest\Models\Operations\ChildOperationItem;
use Tinkoff\Invest\Transport\HttpClientInterface;
use Tinkoff\Invest\Models\DataTypes\MoneyValue;
use Tinkoff\Invest\Models\DataTypes\Quotation;
use Tinkoff\Invest\Models\Operations\Operation;
use Tinkoff\Invest\Models\Operations\OperationsResponse;
use Tinkoff\Invest\Models\Operations\Trade;
use Tinkoff\Invest\Exceptions\Services\OperationsServiceException;
use DateTimeInterface;

/**
 * Сервис работы с операциями.
 * @see https://russianinvestments.github.io/investAPI/operations/
 */
class OperationsService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Получить список операций по счёту
     * @param string $accountId Идентификатор счёта
     * @param DateTimeInterface $from Начало периода
     * @param DateTimeInterface $to Конец периода
     * @param string $state Статус операций (default: OPERATION_STATE_EXECUTED)
     * @param string $figi FIGI инструмента (optional)
     * @param string|null $cursor Пагинация (optional)
     * @param int $limit Лимит операций (default: 1000, max: 1000)
     * @throws OperationsServiceException
     */
    public function getOperations(
        string $accountId,
        DateTimeInterface $from,
        DateTimeInterface $to,
        string $state = 'OPERATION_STATE_EXECUTED',
        string $figi = '',
        ?string $cursor = null,
        int $limit = 1000
    ): OperationsResponse {
        // Валидация входных параметров
        if ($from > $to) {
            throw OperationsServiceException::invalidDateRange($from, $to);
        }

        if ($limit < 1 || $limit > 1000) {
            throw OperationsServiceException::invalidLimit($limit);
        }

        if (!empty($cursor) && !preg_match('/^[a-zA-Z0-9_-]+$/', $cursor)) {
            throw OperationsServiceException::invalidCursor($cursor);
        }

        try {
            $response = $this->httpClient->post(
                'tinkoff.public.invest.api.contract.v1.OperationsService/GetOperations',
                [
                    'accountId' => $accountId,
                    'from' => $from->format(DateTimeInterface::ATOM),
                    'to' => $to->format(DateTimeInterface::ATOM),
                    'state' => $state,
                    'figi' => $figi,
                    'cursor' => $cursor,
                    'limit' => $limit
                ]
            );

            return $this->transformResponse($response);
        } catch (OperationsServiceException $e) {
            throw $e;
        } catch (\Throwable $e) {
            // Анализ возможных ошибок API
            if (str_contains($e->getMessage(), 'account not found')) {
                throw OperationsServiceException::accountNotFound($accountId);
            } elseif (str_contains($e->getMessage(), 'instrument not found')) {
                throw OperationsServiceException::instrumentNotFound($figi);
            } elseif (str_contains($e->getMessage(), 'access denied')) {
                throw OperationsServiceException::accessDenied($accountId);
            } elseif (str_contains($e->getMessage(), 'invalid state')) {
                throw OperationsServiceException::invalidOperationState($state);
            }

            throw OperationsServiceException::serviceUnavailable('GetOperations', $e);
        }
    }

    /**
     * @throws OperationsServiceException
     */
    private function transformResponse(array $response): OperationsResponse
    {
        if (!isset($response['operations']) || !is_array($response['operations'])) {
            throw OperationsServiceException::invalidOperationsResponse($response);
        }

        try {
            return new OperationsResponse(
                operations: array_filter(array_map(
                    [$this, 'transformOperation'],
                    $response['operations']
                )),
                nextCursor: $response['nextCursor'] ?? null
            );
        } catch (OperationsServiceException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw OperationsServiceException::invalidOperationsResponse($response);
        }
    }

    /**
     * @throws OperationsServiceException
     */
    private function transformOperation(array $data): Operation|ChildOperationItem|null
    {
        try {
            if (!isset($data['id']) && isset($data['instrumentUid'], $data['payment'])) {
                return new ChildOperationItem(
                    instrumentUid: $data['instrumentUid'],
                    payment: MoneyValue::fromApi($data['payment']),
                    parentOperationId: $data['parentOperationId'] ?? null,
                    currency: $data['currency'] ?? null,
                    commission: isset($data['commission']) ? Quotation::fromApi($data['commission']) : null,
                    operationType: $data['operationType'] ?? null,
                    type: $data['type'] ?? null,
                    state: $data['state'] ?? null
                );
            }

            $requiredFields = ['id', 'state', 'date', 'type', 'payment'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    throw OperationsServiceException::missingRequiredField($field);
                }
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
        } catch (OperationsServiceException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw OperationsServiceException::invalidOperationsResponse($data);
        }
    }

    /**
     * @throws OperationsServiceException
     */
    private function transformTrades(array $tradesData): array
    {
        try {
            return array_filter(array_map(
                function (array $tradeData) {
                    $requiredFields = ['tradeId', 'dateTime', 'quantity', 'price'];
                    foreach ($requiredFields as $field) {
                        if (!isset($tradeData[$field])) {
                            throw OperationsServiceException::invalidTradeData($tradeData);
                        }
                    }

                    try {
                        return new Trade(
                            tradeId: $tradeData['tradeId'],
                            dateTime: new \DateTime($tradeData['dateTime']),
                            quantity: $tradeData['quantity'],
                            price: MoneyValue::fromApi($tradeData['price']),
                            orderId: $tradeData['orderId'] ?? null
                        );
                    } catch (\Throwable $e) {
                        throw OperationsServiceException::invalidTradeData($tradeData);
                    }
                },
                $tradesData
            ));
        } catch (OperationsServiceException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw OperationsServiceException::invalidTradeData($tradesData);
        }
    }
}
