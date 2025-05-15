<?php

namespace PHPArchitectureGuardian\Utils;

use FilesystemIterator;

/**
 * File system utilities for the analyzer
 */
class FileSystem
{
    /**
     * Find all PHP files in the given directory
     *
     * @param string $path Directory to scan
     * @return array Array of PHP file paths
     */
    public function findPhpFiles(string $path): array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
        );

        $files = [];

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Read file content
     *
     * @param string $filePath
     * @return string
     */
    public function readFile(string $filePath): string
    {
        return file_get_contents($filePath);
    }

    /**
     * Check if file exists
     *
     * @param string $filePath
     * @return bool
     */
    public function fileExists(string $filePath): bool
    {
        return file_exists($filePath);
    }

    /**
     * Get file extension
     *
     * @param string $filePath
     * @return string
     */
    public function getExtension(string $filePath): string
    {
        return pathinfo($filePath, PATHINFO_EXTENSION);
    }
}
