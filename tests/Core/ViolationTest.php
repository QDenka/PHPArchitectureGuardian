<?php

namespace PHPArchitectureGuardian\Tests\Core;

use PHPArchitectureGuardian\Core\Violation;
use PHPUnit\Framework\TestCase;

class ViolationTest extends TestCase
{
    public function testCreateViolation(): void
    {
        $violation = new Violation(
            'path/to/file.php',
            'Test violation message',
            'test.rule',
            3,
            ['detail1' => 'value1']
        );

        $this->assertSame('path/to/file.php', $violation->getFilePath());
        $this->assertSame('Test violation message', $violation->getMessage());
        $this->assertSame('test.rule', $violation->getRuleName());
        $this->assertSame(3, $violation->getSeverity());
        $this->assertSame(['detail1' => 'value1'], $violation->getDetails());
    }

    public function testSeverityIsConstrainedToValidRange(): void
    {
        // Test with severity below minimum
        $lowViolation = new Violation(
            'path/to/file.php',
            'Test message',
            'test.rule',
            -5
        );
        $this->assertSame(1, $lowViolation->getSeverity());

        // Test with severity above maximum
        $highViolation = new Violation(
            'path/to/file.php',
            'Test message',
            'test.rule',
            10
        );
        $this->assertSame(5, $highViolation->getSeverity());
    }

    public function testToString(): void
    {
        $violation = new Violation(
            'path/to/file.php',
            'Test violation message',
            'test.rule',
            3
        );

        $expectedString = '[***] test.rule: Test violation message in path/to/file.php';
        $this->assertSame($expectedString, $violation->toString());
    }
}
