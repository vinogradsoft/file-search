<?php
declare(strict_types=1);

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Vinograd\FileSearch\BasenameFilter;

class BasenameFilterTest extends TestCase
{

    public function testFilter()
    {
        $basenameFilter = new BasenameFilter();
        $basenameFilter->setConfiguration('FileName.php');
        $coolNode = 'var' . DIRECTORY_SEPARATOR . 'FileName.php';

        self::assertTrue($basenameFilter->filter($coolNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . 'FileName.csv';

        self::assertFalse($basenameFilter->filter($badNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . 'FileName';

        self::assertFalse($basenameFilter->filter($badNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . 'FileName.';

        self::assertFalse($basenameFilter->filter($badNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . 'php.FileName';

        self::assertFalse($basenameFilter->filter($badNode));

        $basenameFilter->setConfiguration('directory');
        $coolNode = 'var' . DIRECTORY_SEPARATOR . 'directory';
        self::assertTrue($basenameFilter->filter($coolNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . '.directory';

        self::assertFalse($basenameFilter->filter($badNode));


        $badNode = 'directory' . DIRECTORY_SEPARATOR . 'var';

        self::assertFalse($basenameFilter->filter($badNode));


        $basenameFilter->setConfiguration('file.php.ztp');
        $coolNode = 'var' . DIRECTORY_SEPARATOR . 'file.php.ztp';
        self::assertTrue($basenameFilter->filter($coolNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . 'file.php';

        self::assertFalse($basenameFilter->filter($badNode));
    }

}
