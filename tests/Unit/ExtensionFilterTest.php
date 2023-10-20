<?php
declare(strict_types=1);

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Vinograd\FileSearch\ExtensionFilter;
use Vinograd\Scanner\Exception\ConfigurationException;

class ExtensionFilterTest extends TestCase
{

    public function testFilter()
    {
        $extensionFilter = new ExtensionFilter();
        $extensionFilter->setConfiguration('php');
        $coolNode = 'var' . DIRECTORY_SEPARATOR . 'FileName.php';

        self::assertTrue($extensionFilter->filter($coolNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . 'FileName.csv';

        self::assertFalse($extensionFilter->filter($badNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . 'FileName';

        self::assertFalse($extensionFilter->filter($badNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . '.h';

        self::assertFalse($extensionFilter->filter($badNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . 'h.';

        self::assertFalse($extensionFilter->filter($badNode));

        $badNode = 'var' . DIRECTORY_SEPARATOR . '.phpg';

        self::assertFalse($extensionFilter->filter($badNode));
    }

    public function testSetConfiguration()
    {
        $this->expectException(ConfigurationException::class);
        $extensionFilter = new ExtensionFilter();
        $extensionFilter->setConfiguration(['php']);
    }
}
