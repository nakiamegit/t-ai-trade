<?php

namespace Tinkoff\Invest\Exceptions;

use Throwable;

abstract class BaseException extends \RuntimeException
{
    protected array $context = [];
    protected int $httpStatusCode = 500;
    protected string $errorType = 'base_error';

    public function __construct(
        string $message,
        array $context = [],
        int $code = 0,
        ?Throwable $previous = null,
        int $httpStatusCode = 500,
        string $errorType = 'base_error'
    ) {
        $this->context = $context;
        $this->httpStatusCode = $httpStatusCode;
        $this->errorType = $errorType;
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }
    public function getErrorType(): string
    {
        return $this->errorType;
    }

    public function addContext(array $context): self
    {
        $this->context = array_merge($this->context, $context);
        return $this;
    }

    public static function buildExceptionContext(Throwable $e): array
    {
        return [
            'exception_class' => get_class($e),
            'exception_message' => $e->getMessage(),
            'exception_code' => $e->getCode(),
            'exception_file' => $e->getFile(),
            'exception_line' => $e->getLine(),
            'exception_trace' => $e->getTraceAsString()
        ];
    }
}
