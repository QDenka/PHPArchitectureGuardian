<?php

namespace PHPArchitectureGuardian\Core;

/**
 * Interface for architectural analyzers
 */
interface AnalyzerInterface
{
    /**
     * Analyze the given path against architectural rules
     *
     * @param string $path Path to analyze
     * @return ViolationCollection Collection of violations
     */
    public function analyze(string $path): ViolationCollection;

    /**
     * Configure analyzer with specific rules
     *
     * @param array $config Configuration array
     * @return void
     */
    public function configure(array $config): void;

    /**
     * Add a rule to the analyzer
     *
     * @param RuleInterface $rule
     * @return void
     */
    public function addRule(RuleInterface $rule): void;

    /**
     * Get all rules
     *
     * @return RuleInterface[]
     */
    public function getRules(): array;
}
