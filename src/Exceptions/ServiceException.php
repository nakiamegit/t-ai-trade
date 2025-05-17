<?php
namespace Tinkoff\Invest\Exceptions;

use Throwable;

class ServiceException extends ApiException
{
    public static function invalidResponseStructure(string $message, array $response): self
    {
        return new self(
            "Invalid response structure: {$message}",
            ['api_response' => $response]
        );
    }

    public static function serviceUnavailable(string $serviceName, ?Throwable $previous = null): self
    {
        return new self(
            "Service unavailable: {$serviceName}",
            ['service' => $serviceName],
            503,
            $previous
        );
    }
}