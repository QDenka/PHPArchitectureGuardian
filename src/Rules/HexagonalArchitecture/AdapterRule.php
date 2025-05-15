<?php

namespace PHPArchitectureGuardian\Rules\HexagonalArchitecture;

use PHPArchitectureGuardian\Core\Violation;
use PHPArchitectureGuardian\Rules\AbstractRule;

/**
 * Rule to enforce Adapter constraints in Hexagonal Architecture
 */
class AdapterRule extends AbstractRule
{
    /**
     * AdapterRule constructor.
     */
    public function __construct()
    {
        parent::__construct('hexagonal.adapter');
    }

    /**
     * @inheritDoc
     */
    public function check(string $filePath, array $context = []): ?Violation
    {
        // Extract namespace from file
        $namespace = $this->namespaceExtractor->extractFromFile($filePath);

        // Check if this is an adapter namespace
        if (!$this->isAdapterNamespace($namespace)) {
            return null; // Not an adapter, no violation
        }

        // Get the content of the file
        $content = file_get_contents($filePath);
        $className = $this->namespaceExtractor->extractClassNameFromContent($content);

        // Adapters should implement a port/interface from the domain
        $implementsPort = $this->implementsPort($content, $filePath);

        // Configurable: should adapters always implement ports?
        $shouldImplementPort = $this->config['adapters_should_implement_ports'] ?? true;

        if ($shouldImplementPort && !$implementsPort) {
            $message = sprintf(
                "Adapter '%s' should implement a port/interface from the domain",
                $className
            );

            return $this->createViolation(
                $filePath,
                $message,
                3, // Medium severity
                [
                    'class_name' => $className,
                    'namespace' => $namespace,
                ]
            );
        }

        return null;
    }

    /**
     * Check if class implements a port from the domain
     *
     * @param string $content
     * @param string $filePath
     * @return bool
     */
    private function implementsPort(string $content, string $filePath): bool
    {
        $className = $this->namespaceExtractor->extractClassNameFromContent($content);

        // Check for implements keyword in class definition
        $pattern = '/class\s+' . preg_quote($className, '/') . '.*implements\s+([^{]+)/';
        if (preg_match($pattern, $content, $matches)) {
            $implementsList = $matches[1];
            $interfaces = array_map('trim', explode(',', $implementsList));

            // Check if at least one interface is from a port namespace
            $portInterfaces = array_filter($interfaces, function ($interface) use ($content) {
                // Check for fully qualified names in the implements list
                if (strpos($interface, '\\') !== false) {
                    return $this->isPortNamespace($interface);
                }

                // Get use statements
                $usePattern = '/use\s+([^;]+);/';
                preg_match_all($usePattern, $content, $useMatches);

                foreach ($useMatches[1] ?? [] as $use) {
                    // Check if use statement imports this interface
                    if (substr($use, strrpos($use, '\\') + 1) === $interface) {
                        return $this->isPortNamespace($use);
                    }
                }

                return false;
            });

            return !empty($portInterfaces);
        }

        return false;
    }

    /**
     * Check if namespace is an adapter namespace
     *
     * @param string $namespace
     * @return bool
     */
    private function isAdapterNamespace(string $namespace): bool
    {
        $adapterNamespaces = $this->config['adapter_namespaces'] ?? [
            'Infrastructure',
            'Adapter',
            'Framework',
            'UI',
            'Persistence'
        ];
        return $this->namespaceMatches($namespace, $adapterNamespaces);
    }

    /**
     * Check if namespace is a port namespace
     *
     * @param string $namespace
     * @return bool
     */
    private function isPortNamespace(string $namespace): bool
    {
        $portNamespaces = $this->config['port_namespaces'] ?? [
            'Port',
            'Domain\\Port',
            'Application\\Port',
            'Domain\\Contract'
        ];
        return $this->namespaceMatches($namespace, $portNamespaces);
    }
}
