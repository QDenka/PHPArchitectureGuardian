<?php

namespace PHPArchitectureGuardian\Utils;

/**
 * Utility for checking dependencies between files
 */
class DependencyChecker
{
    /**
     * Extract dependencies from a file
     *
     * @param string $filePath
     * @return array
     */
    public function extractDependencies(string $filePath): array
    {
        $content = file_get_contents($filePath);
        return $this->extractDependenciesFromContent($content);
    }

    /**
     * Extract dependencies from content
     *
     * @param string $content
     * @return array
     */
    public function extractDependenciesFromContent(string $content): array
    {
        $dependencies = [];

        // Extract use statements
        $pattern = '/use\s+([^;]+);/';
        preg_match_all($pattern, $content, $matches);

        if (isset($matches[1])) {
            $dependencies = array_merge($dependencies, $matches[1]);
        }

        // Extract type hints in function parameters
        $typeHintPattern = '/function\s+\w+\s*\(.*?(\\\\\w+(?:\\\\\w+)*)\s+\$\w+.*?\)/s';
        preg_match_all($typeHintPattern, $content, $typeHintMatches);

        if (isset($typeHintMatches[1])) {
            $dependencies = array_merge($dependencies, $typeHintMatches[1]);
        }

        // Extract return type hints
        $returnTypePattern = '/function\s+\w+\s*\(.*?\)\s*:\s*(\\\\\w+(?:\\\\\w+)*)/s';
        preg_match_all($returnTypePattern, $content, $returnTypeMatches);

        if (isset($returnTypeMatches[1])) {
            $dependencies = array_merge($dependencies, $returnTypeMatches[1]);
        }

        // Extract constructor property promotion type hints (PHP 8.0+)
        $constructorTypePattern = '/function\s+__construct\s*\(.*?(\\\\\w+(?:\\\\\w+)*)\s+\$\w+.*?\)/s';
        preg_match_all($constructorTypePattern, $content, $constructorTypeMatches);

        if (isset($constructorTypeMatches[1])) {
            $dependencies = array_merge($dependencies, $constructorTypeMatches[1]);
        }

        // Extract property type declarations (PHP 7.4+)
        $propertyTypePattern = '/(?:private|protected|public)\s+(\\\\\w+(?:\\\\\w+)*)\s+\$\w+/';
        preg_match_all($propertyTypePattern, $content, $propertyTypeMatches);

        if (isset($propertyTypeMatches[1])) {
            $dependencies = array_merge($dependencies, $propertyTypeMatches[1]);
        }

        // Remove duplicates and clean up
        $dependencies = array_unique(array_map('trim', $dependencies));

        return $dependencies;
    }

    /**
     * Check if a class depends on another class
     *
     * @param string $sourceFilePath
     * @param string $targetNamespace
     * @return bool
     */
    public function hasDependency(string $sourceFilePath, string $targetNamespace): bool
    {
        $dependencies = $this->extractDependencies($sourceFilePath);

        foreach ($dependencies as $dependency) {
            if ($dependency === $targetNamespace || strpos($dependency, $targetNamespace . '\\') === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a class violates dependency rule (depends on something it shouldn't)
     *
     * @param string $sourceFilePath
     * @param array $forbiddenNamespaces
     * @return array
     */
    public function findForbiddenDependencies(string $sourceFilePath, array $forbiddenNamespaces): array
    {
        $dependencies = $this->extractDependencies($sourceFilePath);
        $violations = [];

        foreach ($dependencies as $dependency) {
            foreach ($forbiddenNamespaces as $forbidden) {
                if ($dependency === $forbidden || strpos($dependency, $forbidden . '\\') === 0) {
                    $violations[] = $dependency;
                    break;
                }
            }
        }

        return $violations;
    }
}
