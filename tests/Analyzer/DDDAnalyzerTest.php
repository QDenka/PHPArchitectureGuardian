<?php

namespace PHPArchitectureGuardian\Tests\Analyzer;

use PHPArchitectureGuardian\Analyzer\DDDAnalyzer;
use PHPArchitectureGuardian\Rules\DDD\DomainLayerRule;
use PHPArchitectureGuardian\Rules\DDD\ApplicationLayerRule;
use PHPArchitectureGuardian\Rules\DDD\InfrastructureLayerRule;
use PHPArchitectureGuardian\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DDDAnalyzerTest extends TestCase
{
    /** @var DDDAnalyzer */
    private DDDAnalyzer $analyzer;

    /** @var FileSystem|MockObject */
    private $fileSystem;

    /** @var DomainLayerRule|MockObject */
    private $domainRule;

    /** @var ApplicationLayerRule|MockObject */
    private $applicationRule;

    /** @var InfrastructureLayerRule|MockObject */
    private $infrastructureRule;

    protected function setUp(): void
    {
        // Mock the FileSystem
        $this->fileSystem = $this->createMock(FileSystem::class);

        // Create the analyzer
        $this->analyzer = new DDDAnalyzer();

        // Using reflection to set the mocked file system
        $reflectionClass = new \ReflectionClass(DDDAnalyzer::class);

        $fileSystemProperty = $reflectionClass->getProperty('fileSystem');
        $fileSystemProperty->setAccessible(true);
        $fileSystemProperty->setValue($this->analyzer, $this->fileSystem);

        // Get the rules property to replace with mocks
        $rulesProperty = $reflectionClass->getProperty('rules');
        $rulesProperty->setAccessible(true);

        // Create mock rules
        $this->domainRule = $this->createMock(DomainLayerRule::class);
        $this->applicationRule = $this->createMock(ApplicationLayerRule::class);
        $this->infrastructureRule = $this->createMock(InfrastructureLayerRule::class);

        // Replace real rules with mocks
        $rulesProperty->setValue($this->analyzer, [
            $this->domainRule,
            $this->applicationRule,
            $this->infrastructureRule
        ]);
    }

    public function testAnalyzerHasCorrectRules(): void
    {
        // Create fresh analyzer to test its default rules
        $analyzer = new DDDAnalyzer();
        $rules = $analyzer->getRules();

        $this->assertCount(3, $rules);

        $ruleClasses = array_map(function ($rule) {
            return get_class($rule);
        }, $rules);

        $this->assertContains(DomainLayerRule::class, $ruleClasses);
        $this->assertContains(ApplicationLayerRule::class, $ruleClasses);
        $this->assertContains(InfrastructureLayerRule::class, $ruleClasses);
    }

    public function testAnalyzeWithConfiguration(): void
    {
        $config = [
            'domain_namespaces' => ['Business', 'Core'],
            'application_namespaces' => ['Service', 'App'],
            'infrastructure_namespaces' => ['External', 'Adapter']
        ];

        // Configure the analyzer
        $this->analyzer->configure($config);

        // Expect each rule to be configured
        $this->domainRule->expects($this->once())
            ->method('configure')
            ->with($this->equalTo($config));

        $this->applicationRule->expects($this->once())
            ->method('configure')
            ->with($this->equalTo($config));

        $this->infrastructureRule->expects($this->once())
            ->method('configure')
            ->with($this->equalTo($config));

        // Run the analyzer (nothing will happen since all is mocked)
        $this->fileSystem->method('findPhpFiles')
            ->willReturn([]);

        $this->analyzer->analyze('/path/to/src');
    }

    public function testAnalyze(): void
    {
        // Prepare the test files
        $testFiles = [
            '/path/to/src/Domain/Entity.php',
            '/path/to/src/Application/Service.php',
            '/path/to/src/Infrastructure/Repository.php'
        ];

        $this->fileSystem->method('findPhpFiles')
            ->willReturn($testFiles);

        // Mock rule responses
        $this->domainRule->method('check')
            ->willReturn(null); // No violations

        $this->applicationRule->method('check')
            ->willReturn(null); // No violations

        $this->infrastructureRule->method('check')
            ->willReturn(null); // No violations

        // Run the analyzer
        $violations = $this->analyzer->analyze('/path/to/src');

        // Expect each rule to be checked for each file
        $this->assertCount(0, $violations);
    }
}
