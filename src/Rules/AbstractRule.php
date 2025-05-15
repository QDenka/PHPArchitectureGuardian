<?php

namespace PHPArchitectureGuardian\Rules;

use PHPArchitectureGuardian\Core\RuleInterface;
use PHPArchitectureGuardian\Core\Violation;
use PHPArchitectureGuardian\Utils\NamespaceExtractor;
use PHPArchitectureGuardian\Utils\DependencyChecker;

/**
 * Abstract base class for all architectural rules
 */
abstract class AbstractRule implements RuleInterface
{
    /** @var string */
    protected string $name;

    /** @var array */
    protected array $config = [];

    /** @var NamespaceExtractor */
    protected NamespaceExtractor $namespaceExtractor;

    /** @var DependencyChecker */
    protected DependencyChecker $dependencyChecker;

    /**
     * AbstractRule constructor.
     *
     * @param string $name
     * @param NamespaceExtractor|null $namespaceExtractor
     * @param DependencyChecker|null $dependencyChecker
     */
    public function __construct(
        string $name,
        ?NamespaceExtractor $namespaceExtractor = null,
        ?DependencyChecker $dependencyChecker = null
    ) {
        $this->name = $name;
        $this->namespaceExtractor = $namespaceExtractor ?? new NamespaceExtractor();
        $this->dependencyChecker = $dependencyChecker ?? new DependencyChecker();
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function configure(array $config): void
    {
        $this->config = $config;
    }

    /**
     * Create a violation instance
     *
     * @param string $filePath
     * @param string $message
     * @param int $severity
     * @param array $details
     * @return Violation
     */
    protected function createViolation(
        string $filePath,
        string $message,
        int $severity = 3,
        array $details = []
    ): Violation {
        return new Violation($filePath, $message, $this->getName(), $severity, $details);
    }

    /**
     * Check if namespace matches any of the patterns
     *
     * @param string $namespace
     * @param array $patterns
     * @return bool
     */
    protected function namespaceMatches(string $namespace, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (str_contains($namespace, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
