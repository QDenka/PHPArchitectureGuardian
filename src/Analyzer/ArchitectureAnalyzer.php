<?php

namespace PHPArchitectureGuardian\Analyzer;

use PHPArchitectureGuardian\Core\AnalyzerInterface;
use PHPArchitectureGuardian\Core\RuleInterface;
use PHPArchitectureGuardian\Core\ViolationCollection;
use PHPArchitectureGuardian\Utils\FileSystem;

/**
 * Base class for all architecture analyzers
 */
abstract class ArchitectureAnalyzer implements AnalyzerInterface
{
    /** @var RuleInterface[] */
    protected array $rules = [];

    /** @var array<string, mixed> */
    protected array $config = [];

    /** @var FileSystem */
    protected FileSystem $fileSystem;

    /**
     * ArchitectureAnalyzer constructor.
     *
     * @param FileSystem|null $fileSystem
     */
    public function __construct(?FileSystem $fileSystem = null)
    {
        $this->fileSystem = $fileSystem ?? new FileSystem();
    }

    /**
     * @inheritDoc
     */
    public function analyze(string $path): ViolationCollection
    {
        $violations = new ViolationCollection();
        $files = $this->fileSystem->findPhpFiles($path);

        foreach ($files as $file) {
            foreach ($this->rules as $rule) {
                $context = [
                    'config' => $this->config,
                    'allFiles' => $files,
                ];

                $violation = $rule->check($file, $context);

                if ($violation !== null) {
                    $violations->add($violation);
                }
            }
        }

        return $violations;
    }

    /**
     * Configure analyzer and pass full configuration to each rule.
     *
     * @inheritDoc
     */
    public function configure(array $config): void
    {
        $this->config = $config;

        foreach ($this->rules as $rule) {
            $rule->configure($config);
        }
    }

    /**
     * @inheritDoc
     */
    public function addRule(RuleInterface $rule): void
    {
        $this->rules[] = $rule;
    }

    /**
     * @inheritDoc
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
