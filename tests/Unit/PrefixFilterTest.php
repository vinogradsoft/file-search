<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Vinograd\FileSearch\PrefixFilter;
use Vinograd\Scanner\Exception\ConfigurationException;

class PrefixFilterTest extends TestCase
{

    public function testFilter()
    {
        $coolNode = 'var' . DIRECTORY_SEPARATOR . 'prefixFileName.php';

        $prefixFilter = new PrefixFilter();
        $prefixFilter->setConfiguration('prefix');
        self::assertTrue($prefixFilter->filter($coolNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . 'FileName.php';

        self::assertFalse($prefixFilter->filter($badNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . '.php';

        self::assertFalse($prefixFilter->filter($badNode));

        $coolNode = 'var' . DIRECTORY_SEPARATOR . 'prefixFileName';
        self::assertTrue($prefixFilter->filter($coolNode));

        $coolNode = 'var' . DIRECTORY_SEPARATOR . 'prefix';
        self::assertTrue($prefixFilter->filter($coolNode));

        $coolNode = 'var' . DIRECTORY_SEPARATOR . 'prefix.';
        self::assertTrue($prefixFilter->filter($coolNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . '.prefix';

        self::assertFalse($prefixFilter->filter($badNode));
    }

    public function testSetConfiguration()
    {
        $this->expectException(ConfigurationException::class);
        $extensionFilter = new PrefixFilter();
        $extensionFilter->setConfiguration(['php']);
    }
}
