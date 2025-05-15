<?php

namespace PHPArchitectureGuardian\Core;

/**
 * Interface for all architecture rule checkers
 */
interface RuleInterface
{
    /**
     * Check if the file complies with this rule
     *
     * @param string $filePath Path to the file to check
     * @param array $context Additional context information
     * @return Violation|null Violation if the rule is broken, null otherwise
     */
    public function check(string $filePath, array $context = []): ?Violation;

    /**
     * Get the rule name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Configure the rule with specific parameters
     *
     * @param array $config
     * @return void
     */
    public function configure(array $config): void;
}
