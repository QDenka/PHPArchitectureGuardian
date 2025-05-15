<?php

namespace PHPArchitectureGuardian\Tests\Rules\DDD;

use PHPArchitectureGuardian\Rules\DDD\DomainLayerRule;
use PHPArchitectureGuardian\Utils\DependencyChecker;
use PHPArchitectureGuardian\Utils\NamespaceExtractor;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DomainLayerRuleTest extends TestCase
{
    /** @var DomainLayerRule */
    private DomainLayerRule $rule;

    /** @var NamespaceExtractor|MockObject */
    private $namespaceExtractor;

    /** @var DependencyChecker|MockObject */
    private $dependencyChecker;

    protected function setUp(): void
    {
        $this->namespaceExtractor = $this->createMock(NamespaceExtractor::class);
        $this->dependencyChecker = $this->createMock(DependencyChecker::class);

        $this->rule = new DomainLayerRule();

        // Using reflection to set the mocked dependencies
        $reflectionClass = new \ReflectionClass(DomainLayerRule::class);

        $namespaceExtractorProperty = $reflectionClass->getProperty('namespaceExtractor');
        $namespaceExtractorProperty->setAccessible(true);
        $namespaceExtractorProperty->setValue($this->rule, $this->namespaceExtractor);

        $dependencyCheckerProperty = $reflectionClass->getProperty('dependencyChecker');
        $dependencyCheckerProperty->setAccessible(true);
        $dependencyCheckerProperty->setValue($this->rule, $this->dependencyChecker);
    }

    public function testCheckReturnsNullForNonDomainFile(): void
    {
        $this->namespaceExtractor
            ->method('extractFromFile')
            ->willReturn('App\\Infrastructure\\Repository');

        $result = $this->rule->check('path/to/file.php');

        $this->assertNull($result);
    }

    public function testCheckReturnsViolationForDomainDependingOnInfrastructure(): void
    {
        $this->namespaceExtractor
            ->method('extractFromFile')
            ->willReturn('App\\Domain\\Entity');

        $this->dependencyChecker
            ->method('extractDependencies')
            ->willReturn([
                'App\\Domain\\ValueObject',
                'App\\Infrastructure\\Repository'
            ]);

        $result = $this->rule->check('path/to/DomainEntity.php');

        $this->assertNotNull($result);
        $this->assertSame('ddd.domain_layer', $result->getRuleName());
        $this->assertStringContainsString('should not depend on application or infrastructure', $result->getMessage());
    }

    public function testCheckReturnsNoViolationForDomainDependingOnlyOnDomain(): void
    {
        $this->namespaceExtractor
            ->method('extractFromFile')
            ->willReturn('App\\Domain\\Entity');

        $this->dependencyChecker
            ->method('extractDependencies')
            ->willReturn([
                'App\\Domain\\ValueObject',
                'App\\Domain\\Service'
            ]);

        $result = $this->rule->check('path/to/DomainEntity.php');

        $this->assertNull($result);
    }

    public function testRuleCanBeConfigured(): void
    {
        $config = [
            'domain_namespaces' => ['Core', 'Business'],
            'application_namespaces' => ['AppService'],
            'infrastructure_namespaces' => ['External', 'Persistence']
        ];

        $this->rule->configure($config);

        // Test with custom domain namespace
        $this->namespaceExtractor
            ->method('extractFromFile')
            ->willReturn('Business\\Entity');

        $this->dependencyChecker
            ->method('extractDependencies')
            ->willReturn([
                'Business\\ValueObject',
                'External\\Database'
            ]);

        $result = $this->rule->check('path/to/BusinessEntity.php');

        $this->assertNotNull($result);
        $this->assertStringContainsString('should not depend on application or infrastructure', $result->getMessage());
    }
}
