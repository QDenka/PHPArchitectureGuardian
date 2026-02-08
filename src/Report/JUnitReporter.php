<?php

namespace PHPArchitectureGuardian\Report;

use PHPArchitectureGuardian\Core\ViolationCollection;

/**
 * JUnit XML reporter for CI/CD integration (GitHub Actions, GitLab CI, Jenkins)
 */
class JUnitReporter implements ReporterInterface
{
    /**
     * @inheritDoc
     */
    public function generate(ViolationCollection $violations, array $options = []): string
    {
        $minSeverity = $options['min_severity'] ?? 1;
        $violations = $violations->filterBySeverity($minSeverity);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $testsuites = $dom->createElement('testsuites');
        $dom->appendChild($testsuites);

        $testsuite = $dom->createElement('testsuite');
        $testsuite->setAttribute('name', 'PHPArchitectureGuardian');
        $testsuite->setAttribute('tests', (string) max($violations->count(), 1));
        $testsuite->setAttribute('failures', (string) $violations->count());
        $testsuite->setAttribute('errors', '0');
        $testsuite->setAttribute('time', '0');
        $testsuites->appendChild($testsuite);

        if ($violations->isEmpty()) {
            $testcase = $dom->createElement('testcase');
            $testcase->setAttribute('name', 'Architecture check passed');
            $testcase->setAttribute('classname', 'PHPArchitectureGuardian');
            $testsuite->appendChild($testcase);

            return $dom->saveXML();
        }

        foreach ($violations as $violation) {
            $testcase = $dom->createElement('testcase');
            $testcase->setAttribute('name', $violation->getRuleName());
            $testcase->setAttribute('classname', $violation->getFilePath());

            $failure = $dom->createElement('failure');
            $failure->setAttribute('type', $this->getSeverityLabel($violation->getSeverity()));
            $failure->setAttribute('message', $violation->getMessage());

            $detailText = sprintf(
                "File: %s\nRule: %s\nSeverity: %s\nMessage: %s",
                $violation->getFilePath(),
                $violation->getRuleName(),
                $this->getSeverityLabel($violation->getSeverity()),
                $violation->getMessage()
            );

            $details = $violation->getDetails();
            if (!empty($details)) {
                $detailText .= "\nDetails: " . json_encode($details, JSON_UNESCAPED_SLASHES);
            }

            $failure->appendChild($dom->createCDATASection($detailText));
            $testcase->appendChild($failure);
            $testsuite->appendChild($testcase);
        }

        return $dom->saveXML();
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

        echo $report;
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
            1 => 'NOTICE',
            2 => 'INFO',
            3 => 'WARNING',
            4 => 'ERROR',
            5 => 'CRITICAL',
            default => 'UNKNOWN',
        };
    }
}
