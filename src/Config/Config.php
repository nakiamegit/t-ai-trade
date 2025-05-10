<?php

namespace Tinkoff\Invest\Config;

use Tinkoff\Invest\Exceptions\ConfigException;

class Config
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->validate();
    }

    public static function fromFile(string $filePath): self
    {
        if (!file_exists($filePath)) {
            throw new ConfigException("Config file not found: {$filePath}");
        }

        $config = require $filePath;
        return new self($config);
    }

    private function validate(): void
    {
        if (empty($this->config['api']['token'])) {
            throw new ConfigException('API token is required');
        }
    }

    public function getApiToken(): string
    {
        return $this->config['api']['token'];
    }

    public function getApiUrl(): string
    {
        return rtrim($this->config['api']['url'], '/') . '/';
    }

    public function getApiTimeout(): int
    {
        return $this->config['api']['timeout'] ?? 10;
    }

    public function isLoggingEnabled(): bool
    {
        return $this->config['logging']['enabled'] ?? false;
    }

    public function getLogPath(): string
    {
        return $this->config['logging']['path'] ?? __DIR__ . '/../../logs/app.log';
    }

    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }
}