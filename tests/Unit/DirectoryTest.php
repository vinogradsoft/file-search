<?php
declare(strict_types=1);

namespace Test\Unit;

use BadMethodCallException;
use Test\Cases\Dummy\DummyFunctionality;
use Vinograd\FileSearch\Directory;
use PHPUnit\Framework\TestCase;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class DirectoryTest extends TestCase
{
    public function test__call()
    {
        $directory = new Directory('/var/www');

        FileFunctionalitiesContext::registerGlobalFunctionalityForDirectories(
            DummyFunctionality::create($directory), 'get1'
        );
        FileFunctionalitiesContext::registerGlobalFunctionalityForDirectories(
            DummyFunctionality::create($directory), 'get2'
        );
        FileFunctionalitiesContext::registerGlobalFunctionalityForDirectories(
            DummyFunctionality::create($directory), 'get3'
        );

        self::assertEquals('assert', $directory->get1('assert'));
        self::assertEquals('get2', $directory->get2());
        self::assertEquals('/var/www', $directory->get3());
    }

    public function test__callBadMethod()
    {
        $this->expectException(BadMethodCallException::class);
        $directory = new Directory('/var/www');

        FileFunctionalitiesContext::registerGlobalFunctionalityForDirectories(
            DummyFunctionality::create($directory), 'get1'
        );
        FileFunctionalitiesContext::registerGlobalFunctionalityForDirectories(
            DummyFunctionality::create($directory), 'get2'
        );
        FileFunctionalitiesContext::registerGlobalFunctionalityForDirectories(
            DummyFunctionality::create($directory), 'get3'
        );

        $directory->getBadMethod();
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
    }
}
