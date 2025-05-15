<?php

namespace PHPArchitectureGuardian\Report;

use PHPArchitectureGuardian\Core\ViolationCollection;

/**
 * Console reporter for architecture violations
 */
class ConsoleReporter implements ReporterInterface
{
    /** @var bool */
    private bool $useColors;

    /**
     * ConsoleReporter constructor.
     *
     * @param bool $useColors
     */
    public function __construct(bool $useColors = true)
    {
        $this->useColors = $useColors;
    }

    /**
     * @inheritDoc
     */
    public function generate(ViolationCollection $violations, array $options = []): string
    {
        $minSeverity = $options['min_severity'] ?? 1;
        $violations = $violations->filterBySeverity($minSeverity);

        if ($violations->isEmpty()) {
            return $this->success("No architecture violations found!");
        }

        $result = $this->error(sprintf("Found %d architecture violation(s):\n", $violations->count()));

        foreach ($violations as $index => $violation) {
            $severityLabel = $this->getSeverityLabel($violation->getSeverity());

            $result .= sprintf(
                "\n%d) %s in %s\n",
                $index + 1,
                $severityLabel,
                $this->formatFilePath($violation->getFilePath())
            );

            $result .= sprintf("   Rule: %s\n", $violation->getRuleName());
            $result .= sprintf("   %s\n", $violation->getMessage());

            // Add details if available
            $details = $violation->getDetails();
            if (!empty($details)) {
                if (isset($details['forbidden_dependencies']) && !empty($details['forbidden_dependencies'])) {
                    $result .= "   Forbidden dependencies:\n";
                    foreach ($details['forbidden_dependencies'] as $dep) {
                        $result .= sprintf("     - %s\n", $dep);
                    }
                }
            }

            $result .= "\n";
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function output(string $report, array $options = []): void
    {
        echo $report;
    }

    /**
     * Format success message
     *
     * @param string $message
     * @return string
     */
    private function success(string $message): string
    {
        if ($this->useColors) {
            return "\033[32m" . $message . "\033[0m\n";
        }

        return $message . "\n";
    }

    /**
     * Format error message
     *
     * @param string $message
     * @return string
     */
    private function error(string $message): string
    {
        if ($this->useColors) {
            return "\033[31m" . $message . "\033[0m";
        }

        return $message;
    }

    /**
     * Format warning message
     *
     * @param string $message
     * @return string
     */
    private function warning(string $message): string
    {
        if ($this->useColors) {
            return "\033[33m" . $message . "\033[0m";
        }

        return $message;
    }

    /**
     * Format info message
     *
     * @param string $message
     * @return string
     */
    private function info(string $message): string
    {
        if ($this->useColors) {
            return "\033[34m" . $message . "\033[0m";
        }

        return $message;
    }

    /**
     * Format file path
     *
     * @param string $filePath
     * @return string
     */
    private function formatFilePath(string $filePath): string
    {
        if ($this->useColors) {
            return "\033[36m" . $filePath . "\033[0m";
        }

        return $filePath;
    }

    /**
     * Get severity label based on severity level
     *
     * @param int $severity
     * @return string
     */
    private function getSeverityLabel(int $severity): string
    {
        $labels = [
            1 => 'NOTICE',
            2 => 'INFO',
            3 => 'WARNING',
            4 => 'ERROR',
            5 => 'CRITICAL',
        ];

        $label = $labels[$severity] ?? 'UNKNOWN';

        if (!$this->useColors) {
            return $label;
        }

        $colors = [
            1 => "\033[34m", // Blue
            2 => "\033[36m", // Cyan
            3 => "\033[33m", // Yellow
            4 => "\033[31m", // Red
            5 => "\033[37;41m", // White on red
        ];

        $color = $colors[$severity] ?? "\033[0m";

        return $color . $label . "\033[0m";
    }
}
