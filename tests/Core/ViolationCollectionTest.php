<?php

namespace PHPArchitectureGuardian\Tests\Core;

use PHPArchitectureGuardian\Core\Violation;
use PHPArchitectureGuardian\Core\ViolationCollection;
use PHPUnit\Framework\TestCase;

class ViolationCollectionTest extends TestCase
{
    private ViolationCollection $collection;
    private Violation $violation1;
    private Violation $violation2;
    private Violation $violation3;

    protected function setUp(): void
    {
        $this->collection = new ViolationCollection();

        $this->violation1 = new Violation(
            'path/to/file1.php',
            'First violation',
            'rule1',
            1
        );

        $this->violation2 = new Violation(
            'path/to/file2.php',
            'Second violation',
            'rule2',
            3
        );

        $this->violation3 = new Violation(
            'path/to/file3.php',
            'Third violation',
            'rule1',
            5
        );
    }

    public function testAddViolation(): void
    {
        $this->collection->add($this->violation1);
        $this->assertCount(1, $this->collection);

        $this->collection->add($this->violation2);
        $this->assertCount(2, $this->collection);
    }

    public function testMergeCollections(): void
    {
        $collection1 = new ViolationCollection();
        $collection1->add($this->violation1);

        $collection2 = new ViolationCollection();
        $collection2->add($this->violation2);
        $collection2->add($this->violation3);

        $collection1->merge($collection2);

        $this->assertCount(3, $collection1);
    }

    public function testFilterBySeverity(): void
    {
        $this->collection->add($this->violation1); // Severity 1
        $this->collection->add($this->violation2); // Severity 3
        $this->collection->add($this->violation3); // Severity 5

        $filteredCollection = $this->collection->filterBySeverity(3);

        $this->assertCount(2, $filteredCollection);
        $violations = $filteredCollection->toArray();
        $this->assertSame('Second violation', $violations[0]->getMessage());
        $this->assertSame('Third violation', $violations[1]->getMessage());
    }

    public function testFilterByRule(): void
    {
        $this->collection->add($this->violation1); // Rule 'rule1'
        $this->collection->add($this->violation2); // Rule 'rule2'
        $this->collection->add($this->violation3); // Rule 'rule1'

        $filteredCollection = $this->collection->filterByRule('rule1');

        $this->assertCount(2, $filteredCollection);
        $violations = $filteredCollection->toArray();
        $this->assertSame('First violation', $violations[0]->getMessage());
        $this->assertSame('Third violation', $violations[1]->getMessage());
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue($this->collection->isEmpty());

        $this->collection->add($this->violation1);
        $this->assertFalse($this->collection->isEmpty());
    }

    public function testToString(): void
    {
        $this->collection->add($this->violation1);
        $this->collection->add($this->violation2);

        $result = $this->collection->toString();

        $this->assertStringContainsString('Found 2 violation(s)', $result);
        $this->assertStringContainsString('First violation', $result);
        $this->assertStringContainsString('Second violation', $result);
    }

    public function testToStringEmptyCollection(): void
    {
        $result = $this->collection->toString();

        $this->assertSame('No violations found.', $result);
    }
}
