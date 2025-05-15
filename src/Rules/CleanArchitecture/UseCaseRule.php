<?php

namespace PHPArchitectureGuardian\Rules\CleanArchitecture;

use PHPArchitectureGuardian\Core\Violation;
use PHPArchitectureGuardian\Rules\AbstractRule;

/**
 * Rule to enforce Use Case layer constraints in Clean Architecture
 */
class UseCaseRule extends AbstractRule
{
    /**
     * UseCaseRule constructor.
     */
    public function __construct()
    {
        parent::__construct('clean.use_case_layer');
    }

    /**
     * @inheritDoc
     */
    public function check(string $filePath, array $context = []): ?Violation
    {
        // Extract namespace from file
        $namespace = $this->namespaceExtractor->extractFromFile($filePath);

        // Check if this is a use case namespace
        if (!$this->isUseCaseNamespace($namespace)) {
            return null; // Not a use case class, no violation
        }

        // Get file dependencies
        $dependencies = $this->dependencyChecker->extractDependencies($filePath);

        // In Clean Architecture, use cases can depend on entities, but not on controllers or frameworks
        $forbiddenDependencies = array_filter($dependencies, function ($dep) {
            return $this->isControllerNamespace($dep) || $this->isFrameworkNamespace($dep);
        });

        if (!empty($forbiddenDependencies)) {
            $message = sprintf(
                "Use Case layer should not depend on outer layers (controllers, frameworks). Found dependencies: %s",
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
