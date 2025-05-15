<?php

namespace PHPArchitectureGuardian;

use PHPArchitectureGuardian\Analyzer\ArchitectureAnalyzer;
use PHPArchitectureGuardian\Analyzer\DDDAnalyzer;
use PHPArchitectureGuardian\Analyzer\CleanArchitectureAnalyzer;
use PHPArchitectureGuardian\Analyzer\HexagonalArchitectureAnalyzer;
use PHPArchitectureGuardian\Analyzer\CustomAnalyzer;
use PHPArchitectureGuardian\Config\ConfigLoader;
use PHPArchitectureGuardian\Core\ViolationCollection;
use PHPArchitectureGuardian\Report\ReporterInterface;
use PHPArchitectureGuardian\Report\ConsoleReporter;

/**
 * Main class for the PHPArchitectureGuardian tool
 */
class ArchitectureGuardian
{
    /** @var array */
    private array $config;

    /** @var ArchitectureAnalyzer[] */
    private array $analyzers = [];

    /** @var ReporterInterface */
    private ReporterInterface $reporter;

    /**
     * ArchitectureGuardian constructor.
     *
     * @param string|null $configFile
     * @param ReporterInterface|null $reporter
     * @throws \Exception
     */
    public function __construct(?string $configFile = null, ?ReporterInterface $reporter = null)
    {
        $configLoader = new ConfigLoader();
        $this->config = $configLoader->load($configFile);
        $this->reporter = $reporter ?? new ConsoleReporter();

        $this->initializeAnalyzers();
    }

    /**
     * Run the architecture analysis
     *
     * @param string|null $path
     * @return int Exit code (0 = success, 1 = violations found)
     */
    public function run(?string $path = null): int
    {
        $path = $path ?? getcwd();

        if (!is_dir($path)) {
            echo "Error: Path {$path} is not a directory.\n";
            return 1;
        }

        $allViolations = new ViolationCollection();

        foreach ($this->analyzers as $analyzer) {
            $violations = $analyzer->analyze($path);
            $allViolations->merge($violations);
        }

        $report = $this->reporter->generate($allViolations, $this->config['report'] ?? []);
        $this->reporter->output($report, $this->config['report'] ?? []);

        // Return non-zero exit code if violations were found
        return $allViolations->isEmpty() ? 0 : 1;
    }

    /**
     * Initialize analyzers based on configuration
     */
    private function initializeAnalyzers(): void
    {
        $analyzerConfigs = $this->config['analyzers'] ?? [];

        // DDD Analyzer
        if (($analyzerConfigs['ddd']['enabled'] ?? false) === true) {
            $analyzer = new DDDAnalyzer();
            $analyzer->configure($analyzerConfigs['ddd']['config'] ?? []);
            $this->analyzers[] = $analyzer;
        }

        // Clean Architecture Analyzer
        if (($analyzerConfigs['clean']['enabled'] ?? false) === true) {
            $analyzer = new CleanArchitectureAnalyzer();
            $analyzer->configure($analyzerConfigs['clean']['config'] ?? []);
            $this->analyzers[] = $analyzer;
        }

        // Hexagonal Architecture Analyzer
        if (($analyzerConfigs['hexagonal']['enabled'] ?? false) === true) {
            $analyzer = new HexagonalArchitectureAnalyzer();
            $analyzer->configure($analyzerConfigs['hexagonal']['config'] ?? []);
            $this->analyzers[] = $analyzer;
        }

        if (($analyzerConfigs['custom']['enabled'] ?? false) === true) {
            $analyzer = new CustomAnalyzer();
            $analyzer->configure($analyzerConfigs['custom']['config'] ?? []);

            if (isset($analyzerConfigs['custom']['rules'])) {
                foreach ($analyzerConfigs['custom']['rules'] as $ruleClass => $ruleConfig) {
                    try {
                        $analyzer->addCustomRule($ruleClass, $ruleConfig ?? []);
                    } catch (\Exception $e) {
                        echo "Warning: Failed to add custom rule {$ruleClass}: " . $e->getMessage() . "\n";
                    }
                }
            }

            $this->analyzers[] = $analyzer;
        }
    }
}
