<?php

namespace Tinkoff\Invest\Exceptions;

use Throwable;

class ServiceException extends ApiException
{
    public const STATUS_BAD_REQUEST = 400;
    public const STATUS_UNAUTHORIZED = 401;
    public const STATUS_FORBIDDEN = 403;
    public const STATUS_NOT_FOUND = 404;
    public const STATUS_SERVER_ERROR = 500;
    public const STATUS_SERVICE_UNAVAILABLE = 503;

    public static function invalidResponseStructure(
        array $response,
        ?string $message = null,
        ?Throwable $previous = null
    ): static {
        return new static(
            $message ?? 'Invalid API response structure',
            ['api_response' => $response],
            self::STATUS_SERVER_ERROR,
            $previous
        );
    }

    public static function serviceUnavailable(
        string $serviceName,
        ?Throwable $previous = null
    ): static {
        return new static(
            "Service temporarily unavailable: {$serviceName}",
            ['service' => $serviceName],
            self::STATUS_SERVICE_UNAVAILABLE,
            $previous
        );
    }

    public static function badRequest(
        string $message,
        array $context = [],
        ?Throwable $previous = null
    ): static {
        return new static(
            $message,
            $context,
            self::STATUS_BAD_REQUEST,
            $previous
        );
    }

    public static function notFound(
        string $resourceType,
        string $resourceId,
        ?Throwable $previous = null
    ): static {
        return new static(
            "{$resourceType} not found: {$resourceId}",
            [
                'resource_type' => $resourceType,
                'resource_id' => $resourceId
            ],
            self::STATUS_NOT_FOUND,
            $previous
        );
    }
}
