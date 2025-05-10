<?php
// src/Exceptions/ApiException.php

namespace Tinkoff\Invest\Exceptions;

class ApiException extends \RuntimeException
{
    private ?array $responseData;

    public function __construct(
        string $message,
        int $code = 0,
        \Throwable $previous = null,
        ?array $responseData = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->responseData = $responseData;
    }

    public function getResponseData(): ?array
    {
        return $this->responseData;
    }
}