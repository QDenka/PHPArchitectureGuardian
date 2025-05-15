<?php

namespace PHPArchitectureGuardian\Rules\DDD;

use PHPArchitectureGuardian\Core\Violation;
use PHPArchitectureGuardian\Rules\AbstractRule;

/**
 * Rule to enforce Domain Layer constraints in DDD
 */
class DomainLayerRule extends AbstractRule
{
    /**
     * DomainLayerRule constructor.
     */
    public function __construct()
    {
        parent::__construct('ddd.domain_layer');
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

        // In DDD, domain should not depend on application or infrastructure layers
        $forbiddenDependencies = array_filter($dependencies, function ($dep) {
            return $this->isApplicationNamespace($dep) || $this->isInfrastructureNamespace($dep);
        });

        if (!empty($forbiddenDependencies)) {
            $message = sprintf(
                "Domain layer should not depend on application or infrastructure layers. Found dependencies: %s",
                implode(', ', $forbiddenDependencies)
            );

            return $this->createViolation(
                $filePath,
                $message,
                4,
                [
                    'forbidden_dependencies' => $forbiddenDependencies,
                    'all_dependencies' => $dependencies,
                ]
            );
        }

        return null;
    }

    /**
     * Check if namespace belongs to domain layer
     *
     * @param string $namespace
     * @return bool
     */
    private function isDomainNamespace(string $namespace): bool
    {
        $domainNamespaces = $this->config['domain_namespaces'] ?? ['Domain', 'Model'];
        return $this->namespaceMatches($namespace, $domainNamespaces);
    }

    /**
     * Check if namespace belongs to application layer
     *
     * @param string $namespace
     * @return bool
     */
    private function isApplicationNamespace(string $namespace): bool
    {
        $applicationNamespaces = $this->config['application_namespaces'] ?? ['Application', 'App'];
        return $this->namespaceMatches($namespace, $applicationNamespaces);
    }

    /**
     * Check if namespace belongs to infrastructure layer
     *
     * @param string $namespace
     * @return bool
     */
    private function isInfrastructureNamespace(string $namespace): bool
    {
        $infrastructureNamespaces = $this->config['infrastructure_namespaces'] ?? ['Infrastructure', 'Infra'];
        return $this->namespaceMatches($namespace, $infrastructureNamespaces);
    }
}
