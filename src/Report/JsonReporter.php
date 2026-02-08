<?php

namespace PHPArchitectureGuardian\Report;

use PHPArchitectureGuardian\Core\ViolationCollection;

/**
 * JSON reporter for CI/CD integration
 */
class JsonReporter implements ReporterInterface
{
    /**
     * @inheritDoc
     */
    public function generate(ViolationCollection $violations, array $options = []): string
    {
        $minSeverity = $options['min_severity'] ?? 1;
        $violations = $violations->filterBySeverity($minSeverity);

        $data = [
            'total_violations' => $violations->count(),
            'violations' => [],
        ];

        foreach ($violations as $violation) {
            $data['violations'][] = [
                'file' => $violation->getFilePath(),
                'rule' => $violation->getRuleName(),
                'message' => $violation->getMessage(),
                'severity' => $violation->getSeverity(),
                'severity_label' => $this->getSeverityLabel($violation->getSeverity()),
                'details' => $violation->getDetails(),
            ];
        }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @inheritDoc
     */
    public function output(string $report, array $options = []): void
    {
        $outputFile = $options['output'] ?? null;

        if ($outputFile !== null) {
            file_put_contents($outputFile, $report);

            return;
        }

        echo $report . "\n";
    }

    /**
     * Get severity label
     *
     * @param int $severity
     * @return string
     */
    private function getSeverityLabel(int $severity): string
    {
        return match ($severity) {
            1 => 'notice',
            2 => 'info',
            3 => 'warning',
            4 => 'error',
            5 => 'critical',
            default => 'unknown',
        };
    }
}
