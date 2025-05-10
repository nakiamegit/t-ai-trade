<?php

namespace Tinkoff\Invest;

use Tinkoff\Invest\Config\Config;

class Logger
{
    private Config $config;
    private $logFile;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->logFile = null;

        if ($this->config->isLoggingEnabled()) {
            $logPath = $this->config->getLogPath();
            $dir = dirname($logPath);

            if (!is_dir($dir)) {
                if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
                }
            }

            $this->logFile = fopen($logPath, 'a');
        }
    }

    public function log(string $message): void
    {
        if ($this->config->isLoggingEnabled() && is_resource($this->logFile)) {
            $timestamp = date('Y-m-d H:i:s');
            fwrite($this->logFile, "[{$timestamp}] {$message}\n");
        }
    }

    public function __destruct()
    {
        if (is_resource($this->logFile)) {
            fclose($this->logFile);
        }
    }
}