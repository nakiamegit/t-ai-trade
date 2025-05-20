<?php

namespace Rest\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rest\Services\PortfolioService;
use Rest\ApiResponse;
use Tinkoff\Invest\InvestClient;

class PortfolioController extends BaseController implements RequestHandlerInterface
{
    private PortfolioService $service;

    public function __construct(InvestClient $client)
    {
        parent::__construct($client);
        $this->service = new PortfolioService($client);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            return match ($request->getMethod()) {
                'GET' => $this->getPortfolio($request),
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

    private function getPortfolio(ServerRequestInterface $request): ResponseInterface
    {
        $accountId = $request->getQueryParams()['accountId'] ?? null;

        if (!$accountId) {
            return ApiResponse::error(400, 'accountId is required');
        }

        return ApiResponse::success($this->service->getFormattedPortfolio($accountId));
    }
}
