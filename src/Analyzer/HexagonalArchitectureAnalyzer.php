<?php

namespace PHPArchitectureGuardian\Analyzer;

use PHPArchitectureGuardian\Core\ViolationCollection;
use PHPArchitectureGuardian\Rules\HexagonalArchitecture\DomainRule;
use PHPArchitectureGuardian\Rules\HexagonalArchitecture\PortRule;
use PHPArchitectureGuardian\Rules\HexagonalArchitecture\AdapterRule;

/**
 * Analyzer for Hexagonal Architecture (Ports & Adapters)
 */
class HexagonalArchitectureAnalyzer extends ArchitectureAnalyzer
{
    /**
     * HexagonalArchitectureAnalyzer constructor.
     * Initializes with default Hexagonal Architecture rules
     */
    public function __construct()
    {
        parent::__construct();

        $this->addRule(new DomainRule());
        $this->addRule(new PortRule());
        $this->addRule(new AdapterRule());
    }
}
