<?php

namespace PHPArchitectureGuardian\Analyzer;

use PHPArchitectureGuardian\Core\ViolationCollection;
use PHPArchitectureGuardian\Rules\DDD\DomainLayerRule;
use PHPArchitectureGuardian\Rules\DDD\ApplicationLayerRule;
use PHPArchitectureGuardian\Rules\DDD\InfrastructureLayerRule;

/**
 * Analyzer for Domain-Driven Design architecture
 */
class DDDAnalyzer extends ArchitectureAnalyzer
{
    /**
     * DDDAnalyzer constructor.
     * Initializes with default DDD rules
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->addRule(new DomainLayerRule());
        $this->addRule(new ApplicationLayerRule());
        $this->addRule(new InfrastructureLayerRule());
    }

    /**
     * Analyze the given path for DDD architecture violations
     *
     * @param string $path Path to analyze
     * @return ViolationCollection Collection of violations
     */
    public function analyze(string $path): ViolationCollection
    {
        // Use parent implementation for analysis
        return parent::analyze($path);
    }
}
