<?php

namespace PHPArchitectureGuardian\Tests\Utils;

use PHPArchitectureGuardian\Utils\NamespaceExtractor;
use PHPUnit\Framework\TestCase;

class NamespaceExtractorTest extends TestCase
{
    /** @var NamespaceExtractor */
    private NamespaceExtractor $extractor;

    /** @var string */
    private string $tempFile;

    protected function setUp(): void
    {
        $this->extractor = new NamespaceExtractor();
        $this->tempFile = tempnam(sys_get_temp_dir(), 'phpag_test_');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testExtractNamespaceFromContent(): void
    {
        $content = <<<'PHP'
<?php

namespace App\Domain\Entity;

class User
{
    // Class implementation
}
PHP;

        $namespace = $this->extractor->extractFromContent($content);

        $this->assertSame('App\\Domain\\Entity', $namespace);
    }

    public function testExtractEmptyNamespaceFromContentWithoutNamespace(): void
    {
        $content = <<<'PHP'
<?php

class User
{
    // Class implementation
}
PHP;

        $namespace = $this->extractor->extractFromContent($content);

        $this->assertSame('', $namespace);
    }

    public function testExtractClassNameFromContent(): void
    {
        $content = <<<'PHP'
<?php

namespace App\Domain\Entity;

class User
{
    // Class implementation
}
PHP;

        $className = $this->extractor->extractClassNameFromContent($content);

        $this->assertSame('User', $className);
    }

    public function testExtractInterfaceNameFromContent(): void
    {
        $content = <<<'PHP'
<?php

namespace App\Domain\Port;

interface UserRepository
{
    // Interface methods
}
PHP;

        $className = $this->extractor->extractClassNameFromContent($content);

        $this->assertSame('UserRepository', $className);
    }

    public function testExtractTraitNameFromContent(): void
    {
        $content = <<<'PHP'
<?php

namespace App\Common;

trait Loggable
{
    // Trait implementation
}
PHP;

        $className = $this->extractor->extractClassNameFromContent($content);

        $this->assertSame('Loggable', $className);
    }

    public function testExtractFromFile(): void
    {
        $content = <<<'PHP'
<?php

namespace App\Domain\Entity;

class User
{
    // Class implementation
}
PHP;

        file_put_contents($this->tempFile, $content);

        $namespace = $this->extractor->extractFromFile($this->tempFile);

        $this->assertSame('App\\Domain\\Entity', $namespace);
    }

    public function testExtractClassNameFromFile(): void
    {
        $content = <<<'PHP'
<?php

namespace App\Domain\Entity;

class User
{
    // Class implementation
}
PHP;

        file_put_contents($this->tempFile, $content);

        $className = $this->extractor->extractClassNameFromFile($this->tempFile);

        $this->assertSame('User', $className);
    }

    public function testGetFullyQualifiedName(): void
    {
        $content = <<<'PHP'
<?php

namespace App\Domain\Entity;

class User
{
    // Class implementation
}
PHP;

        file_put_contents($this->tempFile, $content);

        $fqn = $this->extractor->getFullyQualifiedName($this->tempFile);

        $this->assertSame('App\\Domain\\Entity\\User', $fqn);
    }

    public function testGetEmptyFullyQualifiedNameForFileWithoutClass(): void
    {
        $content = <<<'PHP'
<?php

namespace App\Domain\Entity;

// No class defined
PHP;

        file_put_contents($this->tempFile, $content);

        $fqn = $this->extractor->getFullyQualifiedName($this->tempFile);

        $this->assertSame('', $fqn);
    }
}
