<?php

namespace Test\Unit;

use Test\Cases\Dummy\DummyFunctionality;
use Vinograd\FileSearch\AbstractSearchFilesystemObject;
use PHPUnit\Framework\TestCase;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class AbstractSearchFilesystemObjectTest extends TestCase
{
    public function testGetSource()
    {
        $object = $this->getMockForAbstractClass(AbstractSearchFilesystemObject::class, ['/var/www']);
        self::assertEquals('/var/www', $object->getSource());
    }

    public function testAssignSupport()
    {
        $object = $this->getMockForAbstractClass(AbstractSearchFilesystemObject::class, ['/var/www']);
        $object->assignSupport(DummyFunctionality::create($object));
        $support = FileFunctionalitiesContext::getFunctionalitySupport($object);
        self::assertTrue($support->has('get1'));
        self::assertTrue($support->has('get2'));
        self::assertTrue($support->has('get3'));
    }


    public function testRevokeSupport()
    {
        $object = $this->getMockForAbstractClass(AbstractSearchFilesystemObject::class, ['/var/www']);
        $object->assignSupport($functionality = DummyFunctionality::create($object));
        $functionalitySupport = FileFunctionalitiesContext::getFunctionalitySupport($object);

        $object->revokeSupport($functionality);

        self::assertFalse($functionalitySupport->has('get1'));
        self::assertFalse($functionalitySupport->has('get2'));
        self::assertFalse($functionalitySupport->has('get3'));
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
    }
}
