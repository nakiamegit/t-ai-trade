<?php

namespace Tinkoff\Invest\Exceptions;

class ConfigException extends ApiException
{
    public static function fileNotFound(string $path): self
    {
        return new self("Config file not found: {$path}", ['path' => $path]);
    }

    public static function invalidConfig(string $message): self
    {
        return new self("Invalid config: {$message}");
    }

    public static function missingParameter(string $param): self
    {
        return new self("Missing required config parameter: {$param}", ['parameter' => $param]);
    }

    public static function invalidType(string $param, string $expectedType, mixed $actualValue): self
    {
        $actualType = gettype($actualValue);
        return new self(
            "Invalid type for config parameter '{$param}': expected {$expectedType}, got {$actualType}",
            [
                'parameter' => $param,
                'expected_type' => $expectedType,
                'actual_type' => $actualType,
                'actual_value' => $actualValue
            ]
        );
    }

    public static function invalidValue(string $param, string $message): self
    {
        return new self(
            "Invalid value for config parameter '{$param}': {$message}",
            ['parameter' => $param]
        );
    }
}