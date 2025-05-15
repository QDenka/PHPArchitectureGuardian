<?php

namespace PHPArchitectureGuardian\Rules\HexagonalArchitecture;

use PHPArchitectureGuardian\Core\Violation;
use PHPArchitectureGuardian\Rules\AbstractRule;

/**
 * Rule to enforce Domain constraints in Hexagonal Architecture (Ports & Adapters)
 */
class DomainRule extends AbstractRule
{
    /**
     * DomainRule constructor.
     */
    public function __construct()
    {
        parent::__construct('hexagonal.domain');
    }

    /**
     * @inheritDoc
     */
    public function check(string $filePath, array $context = []): ?Violation
    {
        // Extract namespace from file
        $namespace = $this->namespaceExtractor->extractFromFile($filePath);

        // Check if this is a domain namespace
        if (!$this->isDomainNamespace($namespace)) {
            return null; // Not a domain class, no violation
        }

        // Get file dependencies
        $dependencies = $this->dependencyChecker->extractDependencies($filePath);

        // In Hexagonal Architecture, domain should not depend on adapters or anything outside the domain
        $forbiddenDependencies = array_filter($dependencies, function ($dep) {
            return $this->isAdapterNamespace($dep) || !$this->isDomainNamespace($dep);
        });

        // Exclude allowed external dependencies (usually standard libraries)
        $allowedExternalDeps = $this->config['allowed_external_dependencies'] ?? [
            'DateTimeInterface',
            'DateTime',
            'DateTimeImmutable',
            'Exception',
            'stdClass'
        ];

        $forbiddenDependencies = array_filter($forbiddenDependencies, function ($dep) use ($allowedExternalDeps) {
            foreach ($allowedExternalDeps as $allowed) {
                if ($dep === $allowed || str_ends_with($dep, '\\' . $allowed)) {
                    return false;
                }
            }
            return true;
        });

        if (!empty($forbiddenDependencies)) {
            $message = sprintf(
                "Domain should not depend on adapters or external code. Found dependencies: %s",
                implode(', ', $forbiddenDependencies)
            );

            return $this->createViolation(
                $filePath,
                $message,
                5, // Highest severity
                [
                    'forbidden_dependencies' => $forbiddenDependencies,
                    'all_dependencies' => $dependencies,
                ]
            );
        }

        return null;
    }

    /**
     * Check if namespace belongs to domain
     *
     * @param string $namespace
     * @return bool
     */
    private function isDomainNamespace(string $namespace): bool
    {
        $domainNamespaces = $this->config['domain_namespaces'] ?? [
            'Domain',
            'Core',
            'Application'
        ];
        return $this->namespaceMatches($namespace, $domainNamespaces);
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
