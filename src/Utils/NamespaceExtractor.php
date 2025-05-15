<?php

namespace PHPArchitectureGuardian\Utils;

/**
 * Namespace extraction utilities
 */
class NamespaceExtractor
{
    /**
     * Extract namespace from file
     *
     * @param string $filePath
     * @return string
     */
    public function extractFromFile(string $filePath): string
    {
        $content = file_get_contents($filePath);
        return $this->extractFromContent($content);
    }

    /**
     * Extract namespace from content
     *
     * @param string $content
     * @return string
     */
    public function extractFromContent(string $content): string
    {
        $pattern = '/namespace\s+([^;]+);/';

        if (preg_match($pattern, $content, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Extract classname from file
     *
     * @param string $filePath
     * @return string
     */
    public function extractClassNameFromFile(string $filePath): string
    {
        $content = file_get_contents($filePath);
        return $this->extractClassNameFromContent($content);
    }

    /**
     * Extract classname from content
     *
     * @param string $content
     * @return string
     */
    public function extractClassNameFromContent(string $content): string
    {
        $patterns = [
            '/class\s+([a-zA-Z0-9_]+)/',
            '/interface\s+([a-zA-Z0-9_]+)/',
            '/trait\s+([a-zA-Z0-9_]+)/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                return $matches[1];
            }
        }

        return '';
    }

    /**
     * Get fully qualified class name from file
     *
     * @param string $filePath
     * @return string
     */
    public function getFullyQualifiedName(string $filePath): string
    {
        $namespace = $this->extractFromFile($filePath);
        $className = $this->extractClassNameFromFile($filePath);

        if (empty($namespace) || empty($className)) {
            return '';
        }

        return $namespace . '\\' . $className;
    }
}
