<?php

namespace PHPArchitectureGuardian\Analyzer;

use PHPArchitectureGuardian\Rules\Custom\NamespaceDependencyRule;
use PHPArchitectureGuardian\Rules\Custom\NamingConventionRule;

/**
 * Analyzer for custom architecture rules
 */
class CustomAnalyzer extends ArchitectureAnalyzer
{
    /**
     * CustomAnalyzer constructor.
     * Initializes with default custom rules
     */
    public function __construct()
    {
        parent::__construct();

        $this->addRule(new NamespaceDependencyRule());
        $this->addRule(new NamingConventionRule());
    }

    /**
     * Add a custom rule to the analyzer
     *
     * @param string $ruleClassName
     * @param array $ruleConfig
     * @return void
     * @throws \Exception If rule class does not exist or is not a valid rule
     */
    public function addCustomRule(string $ruleClassName, array $ruleConfig = []): void
    {
        if (!class_exists($ruleClassName)) {
            throw new \RuntimeException("Rule class {$ruleClassName} does not exist.");
        }

        $rule = new $ruleClassName();

        if (!($rule instanceof \PHPArchitectureGuardian\Core\RuleInterface)) {
            throw new \RuntimeException("Class {$ruleClassName} is not a valid rule.");
        }

        $rule->configure($ruleConfig);
        $this->addRule($rule);
    }
}
