<?php

namespace PHPArchitectureGuardian\Rules\CleanArchitecture;

use PHPArchitectureGuardian\Core\Violation;
use PHPArchitectureGuardian\Rules\AbstractRule;

/**
 * Rule to enforce Entity layer constraints in Clean Architecture
 */
class EntityRule extends AbstractRule
{
    /**
     * EntityRule constructor.
     */
    public function __construct()
    {
        parent::__construct('clean.entity_layer');
    }

    /**
     * @inheritDoc
     */
    public function check(string $filePath, array $context = []): ?Violation
    {
        // Extract namespace from file
        $namespace = $this->namespaceExtractor->extractFromFile($filePath);

        // Check if this is an entity namespace
        if (!$this->isEntityNamespace($namespace)) {
            return null; // Not an entity class, no violation
        }

        // Get file dependencies
        $dependencies = $this->dependencyChecker->extractDependencies($filePath);

        // In Clean Architecture, entities should not depend on any other layer
        $forbiddenDependencies = array_filter($dependencies, function ($dep) {
            return $this->isUseCaseNamespace($dep) ||
                $this->isControllerNamespace($dep) ||
                $this->isFrameworkNamespace($dep);
        });

        if (!empty($forbiddenDependencies)) {
            $message = sprintf(
                "Entity layer should not depend on any other layer (use cases, controllers, frameworks). Found dependencies: %s",
                implode(', ', $forbiddenDependencies)
            );

            return $this->createViolation(
                $filePath,
                $message,
                5, // Highest severity for this rule
                [
                    'forbidden_dependencies' => $forbiddenDependencies,
                    'all_dependencies' => $dependencies,
                ]
            );
        }

        return null;
    }

    /**
     * Check if namespace belongs to entity layer
     *
     * @param string $namespace
     * @return bool
     */
    private function isEntityNamespace(string $namespace): bool
    {
        $entityNamespaces = $this->config['entity_namespaces'] ?? [
            'Entity',
            'Domain\\Entity',
            'Domain\\Model',
            'Core\\Entity'
        ];
        return $this->namespaceMatches($namespace, $entityNamespaces);
    }

    /**
     * Check if namespace belongs to use case layer
     *
     * @param string $namespace
     * @return bool
     */
    private function isUseCaseNamespace(string $namespace): bool
    {
        $useCaseNamespaces = $this->config['use_case_namespaces'] ?? [
            'UseCase',
            'Application',
            'Domain\\UseCase',
            'Core\\UseCase'
        ];
        return $this->namespaceMatches($namespace, $useCaseNamespaces);
    }

    /**
     * Check if namespace belongs to controller layer
     *
     * @param string $namespace
     * @return bool
     */
    private function isControllerNamespace(string $namespace): bool
    {
        $controllerNamespaces = $this->config['controller_namespaces'] ?? [
            'Controller',
            'Interfaces',
            'Presentation',
            'UI'
        ];
        return $this->namespaceMatches($namespace, $controllerNamespaces);
    }

    /**
     * Check if namespace belongs to framework layer
     *
     * @param string $namespace
     * @return bool
     */
    private function isFrameworkNamespace(string $namespace): bool
    {
        $frameworkNamespaces = $this->config['framework_namespaces'] ?? [
            'Framework',
            'Infrastructure',
            'External',
            'Persistence'
        ];
        return $this->namespaceMatches($namespace, $frameworkNamespaces);
    }
}
