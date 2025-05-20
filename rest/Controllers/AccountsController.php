<?php

namespace Rest\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rest\Services\AccountService;
use Rest\ApiResponse;
use Tinkoff\Invest\InvestClient;

class AccountsController extends BaseController implements RequestHandlerInterface
{
    private AccountService $service;

    public function __construct(InvestClient $client)
    {
        parent::__construct($client);
        $this->service = new AccountService($client);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            return match ($request->getMethod()) {
                'GET' => $this->getAllAccounts(),
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

    private function getAllAccounts(): ResponseInterface
    {
        return ApiResponse::success($this->service->getFormattedAccounts());
    }
}
