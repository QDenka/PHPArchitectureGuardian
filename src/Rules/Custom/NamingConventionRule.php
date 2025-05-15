<?php

namespace PHPArchitectureGuardian\Rules\Custom;

use PHPArchitectureGuardian\Core\Violation;
use PHPArchitectureGuardian\Rules\AbstractRule;

/**
 * Rule to enforce patterns for naming conventions
 */
class NamingConventionRule extends AbstractRule
{
    /**
     * NamingConventionRule constructor.
     */
    public function __construct()
    {
        parent::__construct('custom.naming_convention');
    }

    /**
     * @inheritDoc
     */
    public function check(string $filePath, array $context = []): ?Violation
    {
        // Extract namespace and class name
        $namespace = $this->namespaceExtractor->extractFromFile($filePath);
        $content = file_get_contents($filePath);
        $className = $this->namespaceExtractor->extractClassNameFromContent($content);

        if (empty($className)) {
            return null; // Not a class file
        }

        // Find applicable naming rules
        $namingRules = $this->findApplicableNamingRules($namespace);

        if (empty($namingRules)) {
            return null; // No naming rules for this namespace
        }

        // Check if class name matches the pattern
        foreach ($namingRules as $rule) {
            $pattern = $rule['pattern'];
            $description = $rule['description'] ?? 'Must match pattern: ' . $pattern;

            if (!preg_match($pattern, $className)) {
                $message = sprintf(
                    "Class '%s' in namespace '%s' does not follow naming convention: %s",
                    $className,
                    $namespace,
                    $description
                );

                return $this->createViolation(
                    $filePath,
                    $message,
                    2, // Low severity for naming issues
                    [
                        'class_name' => $className,
                        'namespace' => $namespace,
                        'pattern' => $pattern,
                        'description' => $description,
                    ]
                );
            }
        }

        return null;
    }

    /**
     * Find applicable naming rules for a namespace
     *
     * @param string $namespace
     * @return array
     */
    private function findApplicableNamingRules(string $namespace): array
    {
        $rules = $this->config['naming_rules'] ?? [];
        $applicable = [];

        foreach ($rules as $namespacePattern => $namingRules) {
            if (str_starts_with($namespace, $namespacePattern)) {
                $applicable = array_merge($applicable, $namingRules);
            }
        }

        return $applicable;
    }
}
