<?php

namespace PHPArchitectureGuardian\Analyzer;

use PHPArchitectureGuardian\Rules\CleanArchitecture\ControllerRule;
use PHPArchitectureGuardian\Rules\CleanArchitecture\EntityRule;
use PHPArchitectureGuardian\Rules\CleanArchitecture\UseCaseRule;

/**
 * Analyzer for Clean Architecture
 */
class CleanArchitectureAnalyzer extends ArchitectureAnalyzer
{
    /**
     * CleanArchitectureAnalyzer constructor.
     * Initializes with default Clean Architecture rules
     */
    public function __construct()
    {
        parent::__construct();

        $this->addRule(new EntityRule());
        $this->addRule(new UseCaseRule());
        $this->addRule(new ControllerRule());
    }
}
