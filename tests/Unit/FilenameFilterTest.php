<?php
declare(strict_types=1);

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Vinograd\FileSearch\NameFilter;
use Vinograd\Scanner\Exception\ConfigurationException;

class FilenameFilterTest extends TestCase
{

    public function testFilter()
    {
        $filenameFilter = new NameFilter();
        $filenameFilter->setConfiguration('FileName');
        $coolNode = 'var' . DIRECTORY_SEPARATOR . 'FileName.php';

        self::assertTrue($filenameFilter->filter($coolNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . 'FileNam';

        self::assertFalse($filenameFilter->filter($badNode));

        $filenameFilter->setConfiguration('FileName.php');
        $coolNode = 'var' . DIRECTORY_SEPARATOR . 'FileName.php.ted';

        self::assertTrue($filenameFilter->filter($coolNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . 'FileName.php';

        self::assertFalse($filenameFilter->filter($badNode));

    }

    public function testSetConfiguration()
    {
        $this->expectException(ConfigurationException::class);
        $basenameFilter = new NameFilter();
        $basenameFilter->setConfiguration(['php']);
    }
}
