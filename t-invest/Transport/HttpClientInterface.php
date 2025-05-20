<?php

namespace Tinkoff\Invest\Transport;

interface HttpClientInterface
{
    public function request(string $method, string $uri, array $data = []): array;
    public function get(string $uri, array $data = []): array;
    public function post(string $uri, array $data = []): array;
}
