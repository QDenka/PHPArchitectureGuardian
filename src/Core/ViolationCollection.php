<?php

namespace PHPArchitectureGuardian\Core;

/**
 * Collection of architectural violations
 */
class ViolationCollection implements \IteratorAggregate, \Countable
{
    /** @var Violation[] */
    private array $violations = [];

    /**
     * Add a violation to the collection
     *
     * @param Violation $violation
     * @return void
     */
    public function add(Violation $violation): void
    {
        $this->violations[] = $violation;
    }

    /**
     * Merge another collection into this one
     *
     * @param ViolationCollection $collection
     * @return void
     */
    public function merge(ViolationCollection $collection): void
    {
        foreach ($collection as $violation) {
            $this->add($violation);
        }
    }

    /**
     * Get violations filtered by severity
     *
     * @param int $minSeverity Minimum severity level
     * @return ViolationCollection
     */
    public function filterBySeverity(int $minSeverity): ViolationCollection
    {
        $collection = new ViolationCollection();

        foreach ($this->violations as $violation) {
            if ($violation->getSeverity() >= $minSeverity) {
                $collection->add($violation);
            }
        }

        return $collection;
    }

    /**
     * Get violations filtered by rule name
     *
     * @param string $ruleName Rule name to filter by
     * @return ViolationCollection
     */
    public function filterByRule(string $ruleName): ViolationCollection
    {
        $collection = new ViolationCollection();

        foreach ($this->violations as $violation) {
            if ($violation->getRuleName() === $ruleName) {
                $collection->add($violation);
            }
        }

        return $collection;
    }

    /**
     * Get iterator for the collection
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->violations);
    }

    /**
     * Get the count of violations
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->violations);
    }

    /**
     * Check if collection is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->violations);
    }

    /**
     * Get violations as array
     *
     * @return Violation[]
     */
    public function toArray(): array
    {
        return $this->violations;
    }

    /**
     * Get violations as formatted string
     *
     * @return string
     */
    public function toString(): string
    {
        if ($this->isEmpty()) {
            return "No violations found.";
        }

        $result = sprintf("Found %d violation(s):\n\n", $this->count());

        foreach ($this->violations as $index => $violation) {
            $result .= ($index + 1) . ". " . $violation->toString() . "\n";
        }

        return $result;
    }
}
