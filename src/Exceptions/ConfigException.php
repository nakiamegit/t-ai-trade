<?php

namespace Tinkoff\Invest\Exceptions;

class ConfigException extends ApiException
{
    public const ERROR_FILE_NOT_FOUND = 'config_file_not_found';
    public const ERROR_INVALID_CONFIG = 'invalid_config';
    public const ERROR_MISSING_PARAM = 'missing_config_param';
    public const ERROR_INVALID_TYPE = 'invalid_config_type';
    public const ERROR_INVALID_VALUE = 'invalid_config_value';

    public static function fileNotFound(string $path): self
    {
        return new self(
            "Config file not found: {$path}",
            ['path' => $path],
            0,
            null,
            self::HTTP_NOT_FOUND,
            self::ERROR_FILE_NOT_FOUND
        );
    }

    public static function invalidConfig(string $message): self
    {
        return new self(
            "Invalid config: {$message}",
            [],
            0,
            null,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_CONFIG
        );
    }

    public static function missingParameter(string $param): self
    {
        return new self(
            "Missing required config parameter: {$param}",
            ['parameter' => $param],
            0,
            null,
            self::HTTP_BAD_REQUEST,
            self::ERROR_MISSING_PARAM
        );
    }

    public static function invalidType(
        string $param,
        string $expectedType,
        mixed $actualValue
    ): self {
        return new self(
            "Invalid type for config parameter '{$param}': expected {$expectedType}, got " . gettype($actualValue),
            [
                'parameter' => $param,
                'expected_type' => $expectedType,
                'actual_type' => gettype($actualValue),
                'actual_value' => $actualValue
            ],
            0,
            null,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_TYPE
        );
    }

    public static function invalidValue(string $param, string $message): self
    {
        return new self(
            "Invalid value for config parameter '{$param}': {$message}",
            ['parameter' => $param],
            0,
            null,
            self::HTTP_BAD_REQUEST,
            self::ERROR_INVALID_VALUE
        );
    }
}