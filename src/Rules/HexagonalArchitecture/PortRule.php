<?php

namespace PHPArchitectureGuardian\Rules\HexagonalArchitecture;

use PHPArchitectureGuardian\Core\Violation;
use PHPArchitectureGuardian\Rules\AbstractRule;

/**
 * Rule to enforce Port constraints in Hexagonal Architecture
 */
class PortRule extends AbstractRule
{
    /**
     * PortRule constructor.
     */
    public function __construct()
    {
        parent::__construct('hexagonal.port');
    }

    /**
     * @inheritDoc
     */
    public function check(string $filePath, array $context = []): ?Violation
    {
        // Extract namespace from file
        $namespace = $this->namespaceExtractor->extractFromFile($filePath);

        // Check if this is a port namespace
        if (!$this->isPortNamespace($namespace)) {
            return null; // Not a port, no violation
        }

        // Get the content of the file
        $content = file_get_contents($filePath);

        // Ports should be interfaces in Hexagonal Architecture
        if (!$this->isInterface($content)) {
            $message = "Ports in Hexagonal Architecture should be interfaces";

            return $this->createViolation(
                $filePath,
                $message,
                4, // High severity
                [
                    'namespace' => $namespace,
                ]
            );
        }

        // Get file dependencies
        $dependencies = $this->dependencyChecker->extractDependencies($filePath);

        // Ports should only depend on domain entities, not on adapters
        $forbiddenDependencies = array_filter($dependencies, function ($dep) {
            return $this->isAdapterNamespace($dep);
        });

        if (!empty($forbiddenDependencies)) {
            $message = sprintf(
                "Ports should not depend on adapters. Found dependencies: %s",
                implode(', ', $forbiddenDependencies)
            );

            return $this->createViolation(
                $filePath,
                $message,
                4, // High severity
                [
                    'forbidden_dependencies' => $forbiddenDependencies,
                    'all_dependencies' => $dependencies,
                ]
            );
        }

        return null;
    }

    /**
     * Check if file defines an interface
     *
     * @param string $content
     * @return bool
     */
    private function isInterface(string $content): bool
    {
        return preg_match('/\sinterface\s+[a-zA-Z0-9_]+/', $content) === 1;
    }

    /**
     * Check if namespace is a port namespace
     *
     * @param string $namespace
     * @return bool
     */
    private function isPortNamespace(string $namespace): bool
    {
        $portNamespaces = $this->config['port_namespaces'] ?? [
            'Port',
            'Domain\\Port',
            'Application\\Port',
            'Domain\\Contract'
        ];
        return $this->namespaceMatches($namespace, $portNamespaces);
    }

    /**
     * Check if namespace belongs to adapters
     *
     * @param string $namespace
     * @return bool
     */
    private function isAdapterNamespace(string $namespace): bool
    {
        $adapterNamespaces = $this->config['adapter_namespaces'] ?? [
            'Infrastructure',
            'Adapter',
            'Framework',
            'UI',
            'Persistence'
        ];
        return $this->namespaceMatches($namespace, $adapterNamespaces);
    }
}
