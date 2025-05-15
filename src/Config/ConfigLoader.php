<?php

namespace PHPArchitectureGuardian\Config;

/**
 * Configuration loader for PHPArchitectureGuardian
 */
class ConfigLoader
{
    /**
     * Default config file name
     */
    private const string DEFAULT_CONFIG_FILE = '.architecture-guardian.php';

    /**
     * Load configuration from file
     *
     * @param string|null $configFile
     * @return array
     * @throws \Exception If config file is not found or not valid
     */
    public function load(?string $configFile = null): array
    {
        $configFile = $configFile ?? $this->findDefaultConfigFile();

        if (!file_exists($configFile)) {
            throw new \RuntimeException("Config file not found: {$configFile}");
        }

        $config = require $configFile;

        if (!is_array($config)) {
            throw new \RuntimeException("Invalid config file. Expected array, got " . gettype($config));
        }

        return $this->mergeWithDefaults($config);
    }

    /**
     * Find default config file in current directory or parents
     *
     * @return string
     * @throws \Exception If default config file is not found
     */
    private function findDefaultConfigFile(): string
    {
        $directory = getcwd();

        while ($directory !== '/' && $directory !== '') {
            $configFile = $directory . DIRECTORY_SEPARATOR . self::DEFAULT_CONFIG_FILE;

            if (file_exists($configFile)) {
                return $configFile;
            }

            $directory = dirname($directory);
        }

        throw new \RuntimeException("Default config file not found in current directory or parents.");
    }

    /**
     * Merge user config with default config
     *
     * @param array $config
     * @return array
     */
    private function mergeWithDefaults(array $config): array
    {
        $defaults = [
            'analyzers' => [
                'ddd' => [
                    'enabled' => false,
                    'config' => [
                        'domain_namespaces' => ['Domain', 'Model'],
                        'application_namespaces' => ['Application', 'App'],
                        'infrastructure_namespaces' => ['Infrastructure', 'Infra'],
                    ],
                ],
                'clean' => [
                    'enabled' => false,
                    'config' => [
                        'entity_namespaces' => ['Entity', 'Domain\\Entity', 'Domain\\Model'],
                        'use_case_namespaces' => ['UseCase', 'Application', 'Domain\\UseCase'],
                        'controller_namespaces' => ['Controller', 'Interfaces', 'Presentation', 'UI'],
                        'framework_namespaces' => ['Framework', 'Infrastructure', 'External', 'Persistence'],
                    ],
                ],
                'hexagonal' => [
                    'enabled' => false,
                    'config' => [
                        'domain_namespaces' => ['Domain', 'Core', 'Application'],
                        'port_namespaces' => ['Port', 'Domain\\Port', 'Application\\Port', 'Domain\\Contract'],
                        'adapter_namespaces' => ['Infrastructure', 'Adapter', 'Framework', 'UI', 'Persistence'],
                        'adapters_should_implement_ports' => true,
                    ],
                ],
                'custom' => [
                    'enabled' => false,
                    'config' => [
                        'naming_rules' => [],
                        'dependency_rules' => [],
                        'global_allowed_dependencies' => [
                            'DateTimeInterface',
                            'DateTime',
                            'DateTimeImmutable',
                            'Exception',
                            'stdClass'
                        ],
                    ],
                ],
            ],
            'exclude_patterns' => [
                '/vendor/',
                '/tests/',
                '/var/',
                '/cache/',
            ],
            'report' => [
                'format' => 'console',
                'min_severity' => 3,
                'output' => null,
            ],
        ];

        return array_replace_recursive($defaults, $config);
    }
}
