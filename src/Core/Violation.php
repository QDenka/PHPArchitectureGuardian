<?php

namespace PHPArchitectureGuardian\Core;

/**
 * Represents an architectural violation
 */
class Violation
{
    private string $filePath;
    private string $message;
    private string $ruleName;
    private int $severity;
    private array $details;

    /**
     * Violation constructor.
     *
     * @param string $filePath Path to the file where violation occurred
     * @param string $message Description of the violation
     * @param string $ruleName Name of the rule that was violated
     * @param int $severity Severity level (1-5, where 5 is most severe)
     * @param array $details Additional details about the violation
     */
    public function __construct(
        string $filePath,
        string $message,
        string $ruleName,
        int $severity = 3,
        array $details = []
    ) {
        $this->filePath = $filePath;
        $this->message = $message;
        $this->ruleName = $ruleName;
        $this->severity = max(1, min(5, $severity));
        $this->details = $details;
    }

    /**
     * Get file path
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Get violation message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get rule name
     *
     * @return string
     */
    public function getRuleName(): string
    {
        return $this->ruleName;
    }

    /**
     * Get severity level
     *
     * @return int
     */
    public function getSeverity(): int
    {
        return $this->severity;
    }

    /**
     * Get additional details
     *
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * Convert violation to string representation
     *
     * @return string
     */
    public function toString(): string
    {
        $severityStr = str_repeat('*', $this->severity);
        return sprintf("[%s] %s: %s in %s", $severityStr, $this->ruleName, $this->message, $this->filePath);
    }
}
