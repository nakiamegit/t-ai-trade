<?php

namespace Tinkoff\Invest\Exceptions;

class LoggerException extends ApiException
{
    public static function logDirectoryCreationFailed(string $path): self
    {
        return new self(
            "Cannot create log directory: {$path}",
            ['path' => $path]
        );
    }

    public static function logFileOpenFailed(string $path): self
    {
        return new self(
            "Cannot open log file: {$path}",
            ['path' => $path]
        );
    }

    public static function logWriteFailed(string $message): self
    {
        return new self("Log write failed: {$message}");
    }

    public static function invalidLogLevel(string $level): self
    {
        return new self(
            "Invalid log level: {$level}",
            ['level' => $level]
        );
    }

    public static function resourceNotAvailable(): self
    {
        return new self("Log resource is not available");
    }
}