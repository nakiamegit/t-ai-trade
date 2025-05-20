<?php

use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\ServerRequest;
use League\Route\Router;
use League\Route\Strategy\JsonStrategy;
use Rest\Controllers\{AccountsController, BondsController, PortfolioController, OperationsController};
use Tinkoff\Invest\Config;
use Tinkoff\Invest\InvestClient;

require __DIR__ . '/../vendor/autoload.php';

$request = ServerRequest::fromGlobals();
$responseFactory = new HttpFactory();

$router = new Router();
$router->setStrategy(new JsonStrategy($responseFactory));

$client = new InvestClient(Config::fromFile(__DIR__ . '/../config/tinkoff.php'));

// Регистрация маршрутов
$routes = [
    'GET' => [
        '/rest/accounts' => new AccountsController($client),
        '/rest/bonds' => new BondsController($client),
        '/rest/portfolio' => new PortfolioController($client),
        '/rest/operations' => new OperationsController($client)
    ]
];

/*
Examples:
/rest/operations?account_id=2222573370&from=2024-01-01T00:00:00Z&to=2025-10-01T00:00:00Z
TCS00A109ZK1
*/

foreach ($routes['GET'] as $path => $controller) {
    $router->map('GET', $path, [$controller, 'handle']);
    $router->map('OPTIONS', $path, function () use ($responseFactory) {
        return $responseFactory->createResponse(204)
            ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type');
    });
}

try {
    $response = $router->dispatch($request);
} catch (\Throwable $e) {
    $response = $responseFactory->createResponse(500)
        ->withHeader('Content-Type', 'application/json');

    $response->getBody()->write(json_encode([
        'status' => 'error',
        'error' => ['code' => 500, 'message' => $e->getMessage()]
    ]));
}

// Отправка ответа
http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    header("$name: " . implode(', ', $values));
}
echo $response->getBody();
