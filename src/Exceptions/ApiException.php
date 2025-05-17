<?php

namespace Tinkoff\Invest\Exceptions;

use Throwable;

class ApiException extends \RuntimeException
{
    public function __construct(
        string $message,
        private array $context = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public static function create(
        string $message,
        array $context = [],
        ?Throwable $previous = null
    ): static {
        return new static(
            $message,
            $context,
            $previous?->getCode() ?? 0,
            $previous
        );
    }
}