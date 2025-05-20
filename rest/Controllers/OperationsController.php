<?php

namespace Rest\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DateTime;
use Rest\ApiResponse;

class OperationsController extends BaseController
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            return match ($request->getMethod()) {
                'GET' => $this->getOperations($request),
                'OPTIONS' => ApiResponse::emptyResponse()->withStatus(204),
                default => ApiResponse::error(405, 'Method not allowed', [
                    'allowed_methods' => ['GET', 'OPTIONS']
                ]),
            };
        } catch (\Throwable $e) {
            return ApiResponse::error(500, 'Server error', [
                'exception' => $e->getMessage()
            ]);
        }
    }

    private function getOperations(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();

        $required = ['account_id', 'from', 'to'];
        if ($errorResponse = $this->validateRequestParams($request, $required)) {
            return $errorResponse;
        }

        try {
            $response = $this->client->operations()->getOperations(
                $query['account_id'],
                new DateTime($query['from']),
                new DateTime($query['to']),
                $query['state'] ?? 'OPERATION_STATE_EXECUTED'
            );

            $formatted = $this->formatOperations($response->operations ?? []);

            return ApiResponse::success($formatted);
        } catch (\Exception $e) {
            return ApiResponse::error(400, 'Invalid date format');
        }
    }

    /**
     * Проверяет обязательные GET параметры
     */
    private function validateRequestParams(ServerRequestInterface $request, array $required): ?ResponseInterface
    {
        $query = $request->getQueryParams();
        foreach ($required as $param) {
            if (!isset($query[$param])) {
                return ApiResponse::error(400, "Missing required parameter: $param");
            }
        }
        return null;
    }

    /**
     * Форматирует массив операций
     */
    private function formatOperations(array $operations): array
    {
        return array_map([$this, 'formatOperation'], $operations);
    }

    /**
     * Форматирует одну операцию
     */
    private function formatOperation($operation): array
    {
        return [
            'id' => $operation->id,
            'date' => $operation->date->format(\DateTimeInterface::ATOM),
            'type' => $operation->type,
            'state' => $operation->state,
            'payment' => $this->formatMoney($operation->payment),
            'price' => $this->formatMoney($operation->price),
            //'commission' => $this->formatMoney($operation->commission),
            'figi' => $operation->figi,
            'instrument_type' => $operation->instrumentType
        ];
    }
}
