<?php

namespace PHPArchitectureGuardian\Rules\CleanArchitecture;

use PHPArchitectureGuardian\Core\Violation;
use PHPArchitectureGuardian\Rules\AbstractRule;

/**
 * Rule to enforce Controller/Interface layer constraints in Clean Architecture
 */
class ControllerRule extends AbstractRule
{
    /**
     * ControllerRule constructor.
     */
    public function __construct()
    {
        parent::__construct('clean.controller_layer');
    }

    /**
     * @inheritDoc
     */
    public function check(string $filePath, array $context = []): ?Violation
    {
        // Extract namespace from file
        $namespace = $this->namespaceExtractor->extractFromFile($filePath);

        // Check if this is a controller namespace
        if (!$this->isControllerNamespace($namespace)) {
            return null; // Not a controller class, no violation
        }

        // Get file dependencies
        $dependencies = $this->dependencyChecker->extractDependencies($filePath);

        // In Clean Architecture, controllers should not depend on frameworks/infrastructure
        // They should depend on use cases (or application services)

        // First, check if it depends on any use case
        $hasUseCaseDependency = false;
        foreach ($dependencies as $dependency) {
            if ($this->isUseCaseNamespace($dependency)) {
                $hasUseCaseDependency = true;
                break;
            }
        }

        // Check for forbidden dependencies
        $forbiddenDependencies = array_filter($dependencies, function ($dep) {
            return $this->isFrameworkNamespace($dep);
        });

        // Build violations based on findings
        $violations = [];

        // Should depend on use cases (configurable)
        $shouldDependOnUseCases = $this->config['should_depend_on_use_cases'] ?? true;
        if ($shouldDependOnUseCases && !$hasUseCaseDependency) {
            $message = sprintf(
                "Controller should depend on use cases rather than directly on entities"
            );

            $violations[] = $this->createViolation(
                $filePath,
                $message,
                2, // Medium severity
                [
                    'all_dependencies' => $dependencies,
                ]
            );
        }

        // Should not depend on framework
        if (!empty($forbiddenDependencies)) {
            $message = sprintf(
                "Controller should not depend on framework/infrastructure layer. Found dependencies: %s",
                implode(', ', $forbiddenDependencies)
            );

            $violations[] = $this->createViolation(
                $filePath,
                $message,
                3, // High severity for controllers
                [
                    'forbidden_dependencies' => $forbiddenDependencies,
                    'all_dependencies' => $dependencies,
                ]
            );
        }

        return !empty($violations) ? $violations[0] : null;
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
