<?php

namespace PHPArchitectureGuardian\Rules\DDD;

use PHPArchitectureGuardian\Core\Violation;
use PHPArchitectureGuardian\Rules\AbstractRule;

/**
 * Rule to enforce Application Layer constraints in DDD
 */
class ApplicationLayerRule extends AbstractRule
{
    /**
     * ApplicationLayerRule constructor.
     */
    public function __construct()
    {
        parent::__construct('ddd.application_layer');
    }

    /**
     * @inheritDoc
     */
    public function check(string $filePath, array $context = []): ?Violation
    {
        // Extract namespace from file
        $namespace = $this->namespaceExtractor->extractFromFile($filePath);

        // Check if this is an application namespace
        if (!$this->isApplicationNamespace($namespace)) {
            return null; // Not an application class, no violation
        }

        // Get file dependencies
        $dependencies = $this->dependencyChecker->extractDependencies($filePath);

        // Check if app layer can use infrastructure directly
        $canUseInfrastructure = $this->config['can_use_infrastructure'] ?? false;

        if (!$canUseInfrastructure) {
            // In DDD, application typically should not depend directly on infrastructure
            // but should depend on interfaces (ports) defined in the domain
            $forbiddenDependencies = array_filter($dependencies, function ($dep) {
                return $this->isInfrastructureNamespace($dep);
            });

            if (!empty($forbiddenDependencies)) {
                $message = sprintf(
                    "Application layer should not depend directly on infrastructure layer but use interfaces. Found dependencies: %s",
                    implode(', ', $forbiddenDependencies)
                );

                return $this->createViolation(
                    $filePath,
                    $message,
                    3,
                    [
                        'forbidden_dependencies' => $forbiddenDependencies,
                        'all_dependencies' => $dependencies,
                    ]
                );
            }
        }

        return null;
    }

    /**
     * Check if namespace belongs to application layer
     *
     * @param string $namespace
     * @return bool
     */
    private function isApplicationNamespace(string $namespace): bool
    {
        $applicationNamespaces = $this->config['application_namespaces'] ?? ['Application', 'App', 'UseCase'];
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
