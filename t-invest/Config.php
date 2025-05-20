<?php

namespace Tinkoff\Invest;

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
            throw ConfigException::fileNotFound($filePath);
        }

        $config = require $filePath;

        if (!is_array($config)) {
            throw ConfigException::invalidConfig('Config file must return an array');
        }

        return new self($config);
    }

    private function validate(): void
    {
        if (empty($this->config['api']['token'])) {
            throw ConfigException::missingParameter('api.token');
        }

        if (empty($this->config['api']['url'])) {
            throw ConfigException::missingParameter('api.url');
        }

        if (!is_string($this->config['api']['token'])) {
            throw ConfigException::invalidType('api.token', 'string', $this->config['api']['token']);
        }

        if (!is_string($this->config['api']['url'])) {
            throw ConfigException::invalidType('api.url', 'string', $this->config['api']['url']);
        }

        if (isset($this->config['api']['timeout'])) {
            if (!is_int($this->config['api']['timeout'])) {
                throw ConfigException::invalidType('api.timeout', 'integer', $this->config['api']['timeout']);
            }
            if ($this->config['api']['timeout'] <= 0) {
                throw ConfigException::invalidValue('api.timeout', 'Timeout must be positive integer');
            }
        }

        if ($this->config['logging']['enabled'] ?? false) {
            if (!isset($this->config['logging']['path'])) {
                throw ConfigException::missingParameter('logging.path');
            }

            if (!is_string($this->config['logging']['path'])) {
                throw ConfigException::invalidType('logging.path', 'string', $this->config['logging']['path']);
            }

            if (isset($this->config['logging']['full']) && !is_bool($this->config['logging']['full'])) {
                throw ConfigException::invalidType('logging.full', 'boolean', $this->config['logging']['full']);
            }
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

    public function isLoggingFull(): bool
    {
        return $this->config['logging']['full'] ?? false;
    }

    public function isLoggingSensitiveData(): bool
    {
        return $this->config['logging']['sensitiveData'] ?? false;
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
