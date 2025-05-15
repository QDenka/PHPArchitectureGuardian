<?php

namespace PHPArchitectureGuardian\Rules\DDD;

use PHPArchitectureGuardian\Core\Violation;
use PHPArchitectureGuardian\Rules\AbstractRule;

/**
 * Rule to enforce Infrastructure Layer constraints in DDD
 */
class InfrastructureLayerRule extends AbstractRule
{
    /**
     * InfrastructureLayerRule constructor.
     */
    public function __construct()
    {
        parent::__construct('ddd.infrastructure_layer');
    }

    /**
     * @inheritDoc
     */
    public function check(string $filePath, array $context = []): ?Violation
    {
        // Extract namespace from file
        $namespace = $this->namespaceExtractor->extractFromFile($filePath);

        // Check if this is an infrastructure namespace
        if (!$this->isInfrastructureNamespace($namespace)) {
            return null; // Not an infrastructure class, no violation
        }

        // Check if this infrastructure component implements an interface from the domain
        $content = file_get_contents($filePath);
        $className = $this->namespaceExtractor->extractClassNameFromContent($content);

        // Check for implements keyword in class definition
        $pattern = '/class\s+' . preg_quote($className, '/') . '.*implements\s+([^{]+)/';
        if (preg_match($pattern, $content, $matches)) {
            $implementsList = $matches[1];
            $interfaces = array_map('trim', explode(',', $implementsList));

            // Check if at least one interface is from the domain
            $domainInterfaces = array_filter($interfaces, function ($interface) use ($namespace) {
                // Check for fully qualified names in the implements list
                if (strpos($interface, '\\') !== false) {
                    return $this->isDomainNamespace($interface);
                }

                // Get use statements
                $usePattern = '/use\s+([^;]+);/';
                preg_match_all($usePattern, $content, $useMatches);

                foreach ($useMatches[1] ?? [] as $use) {
                    // Check if use statement imports this interface
                    if (substr($use, strrpos($use, '\\') + 1) === $interface) {
                        return $this->isDomainNamespace($use);
                    }
                }

                return false;
            });

            // If infrastructure component should implement domain interfaces but doesn't
            $shouldImplementDomainInterfaces = $this->config['must_implement_domain_interfaces'] ?? true;

            if ($shouldImplementDomainInterfaces && empty($domainInterfaces)) {
                $message = sprintf(
                    "Infrastructure component '%s' should implement at least one domain interface for proper dependency inversion",
                    $className
                );

                return $this->createViolation(
                    $filePath,
                    $message,
                    2,
                    [
                        'class_name' => $className,
                        'implemented_interfaces' => $interfaces,
                    ]
                );
            }
        }

        return null;
    }

    /**
     * Check if namespace belongs to infrastructure layer
     *
     * @param string $namespace
     * @return bool
     */
    private function isInfrastructureNamespace(string $namespace): bool
    {
        $infrastructureNamespaces = $this->config['infrastructure_namespaces'] ?? ['Infrastructure', 'Infra'];
        return $this->namespaceMatches($namespace, $infrastructureNamespaces);
    }

    /**
     * Check if namespace belongs to domain layer
     *
     * @param string $namespace
     * @return bool
     */
    private function isDomainNamespace(string $namespace): bool
    {
        $domainNamespaces = $this->config['domain_namespaces'] ?? ['Domain', 'Model'];
        return $this->namespaceMatches($namespace, $domainNamespaces);
    }
}
