<?php

namespace PHPArchitectureGuardian\Report;

use PHPArchitectureGuardian\Core\ViolationCollection;

/**
 * Interface for all reporters
 */
interface ReporterInterface
{
    /**
     * Generate a report from violations
     *
     * @param ViolationCollection $violations
     * @param array $options
     * @return string
     */
    public function generate(ViolationCollection $violations, array $options = []): string;

    /**
     * Output report to destination (console, file, etc.)
     *
     * @param string $report
     * @param array $options
     * @return void
     */
    public function output(string $report, array $options = []): void;
}
