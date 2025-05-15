<?php

namespace PHPArchitectureGuardian\Rules\Custom;

use PHPArchitectureGuardian\Core\Violation;
use PHPArchitectureGuardian\Rules\AbstractRule;

/**
 * Rule to enforce custom namespace dependencies
 */
class NamespaceDependencyRule extends AbstractRule
{
    /**
     * NamespaceDependencyRule constructor.
     */
    public function __construct()
    {
        parent::__construct('custom.namespace_dependency');
    }

    /**
     * @inheritDoc
     */
    public function check(string $filePath, array $context = []): ?Violation
    {
        // Get the current namespace
        $namespace = $this->namespaceExtractor->extractFromFile($filePath);

        // Get all dependencies from file
        $dependencies = $this->dependencyChecker->extractDependencies($filePath);

        // Get allowed dependencies configuration
        $allowedDeps = $this->getAllowedDependencies($namespace);

        // If no rules defined for this namespace, no violation
        if ($allowedDeps === null) {
            return null;
        }

        // Find forbidden dependencies
        $forbiddenDependencies = [];

        foreach ($dependencies as $dependency) {
            $isAllowed = false;

            // Check if dependency is explicitly allowed
            foreach ($allowedDeps as $allowed) {
                if ($dependency === $allowed || strpos($dependency, $allowed . '\\') === 0) {
                    $isAllowed = true;
                    break;
                }
            }

            // Check if dependency is in global allowed list
            $globalAllowed = $this->config['global_allowed_dependencies'] ?? [
                'DateTimeInterface',
                'DateTime',
                'DateTimeImmutable',
                'Exception',
                'stdClass'
            ];

            foreach ($globalAllowed as $allowed) {
                if ($dependency === $allowed || str_ends_with($dependency, '\\' . $allowed)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                $forbiddenDependencies[] = $dependency;
            }
        }

        if (!empty($forbiddenDependencies)) {
            $message = sprintf(
                "Namespace '%s' has forbidden dependencies: %s",
                $namespace,
                implode(', ', $forbiddenDependencies)
            );

            return $this->createViolation(
                $filePath,
                $message,
                3,
                [
                    'namespace' => $namespace,
                    'forbidden_dependencies' => $forbiddenDependencies,
                    'allowed_dependencies' => $allowedDeps,
                    'all_dependencies' => $dependencies,
                ]
            );
        }

        return null;
    }

    /**
     * Get allowed dependencies for a namespace
     *
     * @param string $namespace
     * @return array|null
     */
    private function getAllowedDependencies(string $namespace): ?array
    {
        $rules = $this->config['dependency_rules'] ?? [];

        foreach ($rules as $pattern => $allowed) {
            if (str_starts_with($namespace, $pattern)) {
                return $allowed;
            }
        }

        return null;
    }
}
