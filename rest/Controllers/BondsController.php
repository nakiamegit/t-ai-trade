<?php

namespace Rest\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rest\Services\BondService;
use Rest\ApiResponse;
use Tinkoff\Invest\InvestClient;

class BondsController extends BaseController implements RequestHandlerInterface
{
    private BondService $service;

    public function __construct(InvestClient $client)
    {
        parent::__construct($client);
        $this->service = new BondService($client);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            return match ($request->getMethod()) {
                'GET' => $this->getBonds($request),
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

    private function getBonds(ServerRequestInterface $request): ResponseInterface
    {
        $figi = $request->getQueryParams()['figi'] ?? null;

        return $figi
            ? ApiResponse::success($this->service->getFormattedBondByFigi($figi))
            : ApiResponse::success($this->service->getFormattedBonds());
    }
}
