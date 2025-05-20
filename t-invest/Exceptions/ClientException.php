<?php

namespace Tinkoff\Invest\Exceptions;

use Throwable;

class ClientException extends ApiException
{
    // Client-specific error types
    public const ERROR_CLIENT_INIT = 'client_init_error';
    public const ERROR_SERVICE_UNAVAILABLE = 'service_unavailable_error';
    public const ERROR_INVALID_ARGUMENT = 'invalid_argument_error';
    public const ERROR_LOGIC = 'logic_error';

    public static function invalidArgument(
        string $message,
        array $context = [],
        ?Throwable $previous = null
    ): self {
        return new self(
            "Invalid argument: {$message}",
            array_merge(['argument_error' => $message], $context),
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_ARGUMENT
        );
    }

    public static function logicError(
        string $message,
        array $context = [],
        ?Throwable $previous = null
    ): self {
        return new self(
            "Logic error: {$message}",
            array_merge(['logic_error' => $message], $context),
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_INTERNAL_ERROR,
            self::ERROR_LOGIC
        );
    }

    public static function serviceNotAvailable(
        string $serviceName,
        ?Throwable $previous = null,
        array $additionalContext = []
    ): self {
        $context = ['service' => $serviceName];
        if ($previous) {
            $context = array_merge($context, self::buildExceptionContext($previous));
        }
        $context = array_merge($context, $additionalContext);

        return new self(
            "Service '{$serviceName}' is not available",
            $context,
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_SERVICE_UNAVAILABLE,
            self::ERROR_SERVICE_UNAVAILABLE
        );
    }

    public static function invalidClientState(
        string $message,
        array $context = [],
        ?Throwable $previous = null
    ): self {
        return new self(
            "Invalid client state: {$message}",
            array_merge(['state_error' => $message], $context),
            $previous?->getCode() ?? 0,
            $previous,
            self::HTTP_INTERNAL_ERROR,
            self::ERROR_CLIENT_INIT
        );
    }
}
