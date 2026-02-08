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

        if ($content === false) {
            return '';
        }

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

        if ($content === false) {
            return '';
        }

        return $this->extractClassNameFromContent($content);
    }

    /**
     * Extract classname from content.
     * Only matches actual class/interface/trait declarations, not mentions in comments.
     *
     * @param string $content
     * @return string
     */
    public function extractClassNameFromContent(string $content): string
    {
        $patterns = [
            '/^\s*(?:abstract\s+|final\s+|readonly\s+)*class\s+([a-zA-Z0-9_]+)/m',
            '/^\s*(?:readonly\s+)?interface\s+([a-zA-Z0-9_]+)/m',
            '/^\s*trait\s+([a-zA-Z0-9_]+)/m',
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
