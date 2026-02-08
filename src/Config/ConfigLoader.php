<?php

namespace PHPArchitectureGuardian\Config;

/**
 * Loads configuration for ArchitectureGuardian
 */
class ConfigLoader
{
    /** @var string */
    private const DEFAULT_CONFIG_FILE = '.architecture-guardian.php';

    /**
     * Load configuration from file
     *
     * @param string|null $configFile
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function load(?string $configFile = null): array
    {
        $configFile = $configFile ?? $this->findConfigFile();

        if ($configFile === null) {
            return $this->getDefaultConfig();
        }

        if (!file_exists($configFile)) {
            throw new \RuntimeException("Configuration file not found: {$configFile}");
        }

        $config = require $configFile;

        if (!is_array($config)) {
            throw new \RuntimeException("Configuration file must return an array: {$configFile}");
        }

        return array_merge($this->getDefaultConfig(), $config);
    }

    /**
     * Find configuration file in current directory or parent directories
     *
     * @return string|null
     */
    private function findConfigFile(): ?string
    {
        $directory = getcwd();

        if ($directory === false) {
            return null;
        }

        while (true) {
            $configFile = $directory . DIRECTORY_SEPARATOR . self::DEFAULT_CONFIG_FILE;

            if (file_exists($configFile)) {
                return $configFile;
            }

            $parentDirectory = dirname($directory);

            if ($parentDirectory === $directory) {
                break;
            }

            $directory = $parentDirectory;
        }

        return null;
    }

    /**
     * Get default configuration
     *
     * @return array<string, mixed>
     */
    private function getDefaultConfig(): array
    {
        return [
            'analyzers' => [
                'ddd' => [
                    'enabled' => false,
                    'config' => [],
                ],
                'clean' => [
                    'enabled' => false,
                    'config' => [],
                ],
                'hexagonal' => [
                    'enabled' => false,
                    'config' => [],
                ],
                'custom' => [
                    'enabled' => false,
                    'config' => [],
                ],
            ],
            'exclude_patterns' => [
                '/vendor/',
                '/tests/',
            ],
            'report' => [
                'format' => 'console',
                'min_severity' => 1,
                'output' => null,
            ],
        ];
    }
}
