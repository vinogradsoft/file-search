<?php
declare(strict_types=1);

namespace Test\Unit;

use BadMethodCallException;
use Test\Cases\Dummy\DummyFunctionality;
use Vinograd\FileSearch\File;
use PHPUnit\Framework\TestCase;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class FileTest extends TestCase
{
    public function test__call()
    {
        $file = new File('/var/www/file.txt');

        FileFunctionalitiesContext::registerGlobalFunctionalityForFiles(
            DummyFunctionality::create($file), 'get1'
        );
        FileFunctionalitiesContext::registerGlobalFunctionalityForFiles(
            DummyFunctionality::create($file), 'get2'
        );
        FileFunctionalitiesContext::registerGlobalFunctionalityForFiles(
            DummyFunctionality::create($file), 'get3'
        );

        self::assertEquals('assert', $file->get1('assert'));
        self::assertEquals('get2', $file->get2());
        self::assertEquals('/var/www/file.txt', $file->get3());
    }

    public function test__callBadMethod()
    {
        $this->expectException(BadMethodCallException::class);
        $file = new File('/var/www/file.txt');

        FileFunctionalitiesContext::registerGlobalFunctionalityForFiles(
            DummyFunctionality::create($file), 'get1'
        );
        FileFunctionalitiesContext::registerGlobalFunctionalityForFiles(
            DummyFunctionality::create($file), 'get2'
        );
        FileFunctionalitiesContext::registerGlobalFunctionalityForFiles(
            DummyFunctionality::create($file), 'get3'
        );

        $file->getBadMethod();
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
    }
}
