<?php
declare(strict_types=1);

namespace Test\Unit;

use Test\Cases\ScannerFactoryCase;
use Vinograd\FilesDriver\FilesystemDriver;
use Vinograd\FileSearch\ProxyVisitor;
use Vinograd\FileSearch\ScannerFactory;
use Vinograd\FileSearch\SecondLevelFilter;
use Vinograd\Scanner\AbstractTraversalStrategy;
use Vinograd\Scanner\BreadthStrategy;
use Vinograd\Scanner\Driver;
use Vinograd\Scanner\Filter;
use Vinograd\Scanner\Visitor;

class ScannerFactoryTest extends ScannerFactoryCase
{

    public function testSetDriver()
    {
        $scannerFactory = new ScannerFactory();
        $scannerFactory->setDriver($driver = $this->getMockForAbstractClass(Driver::class));
        self::assertSame($driver, $scannerFactory->getDriver());
    }

    public function testSetLeafFilters()
    {
        $scannerFactory = new ScannerFactory();
        self::assertEmpty($scannerFactory->getFileFilters());
        $scannerFactory->setFileFilters([
            $filter1 = $this->getMockForAbstractClass(Filter::class),
            $filter2 = $this->getMockForAbstractClass(Filter::class),
            $filter3 = $this->getMockForAbstractClass(Filter::class),
        ]);
        $filters = $scannerFactory->getFileFilters();
        self::assertContains($filter1, $filters);
        self::assertContains($filter2, $filters);
        self::assertContains($filter3, $filters);
    }

    public function testSetNodeFilters()
    {
        $scannerFactory = new ScannerFactory();
        self::assertEmpty($scannerFactory->getDirectoryFilters());
        $scannerFactory->setDirectoryFilters([
            $filter1 = $this->getMockForAbstractClass(Filter::class),
            $filter2 = $this->getMockForAbstractClass(Filter::class),
            $filter3 = $this->getMockForAbstractClass(Filter::class),
        ]);
        $filters = $scannerFactory->getDirectoryFilters();
        self::assertContains($filter1, $filters);
        self::assertContains($filter2, $filters);
        self::assertContains($filter3, $filters);
    }

    public function testSetNodeTargetHandler()
    {
        $scannerFactory = new ScannerFactory();
        self::assertTrue($scannerFactory->isDirectoryMultiTarget());
        $scannerFactory->setDirectorySecondLevelFilter(
            $handler = $this->getMockForAbstractClass(SecondLevelFilter::class),
            false
        );
        self::assertFalse($scannerFactory->isDirectoryMultiTarget());
        self::assertSame($handler, $scannerFactory->getDirectorySecondLevelFilter());
    }

    public function testSetLeafTargetHandler()
    {
        $scannerFactory = new ScannerFactory();
        self::assertTrue($scannerFactory->isFileMultiTarget());
        $scannerFactory->setFileSecondLevelFilter(
            $handler = $this->getMockForAbstractClass(SecondLevelFilter::class),
            false
        );
        self::assertFalse($scannerFactory->isFileMultiTarget());
        self::assertSame($handler, $scannerFactory->getFileSecondLevelFilter());
    }

    public function testNewInstance()
    {
        $scannerFactory = new ScannerFactory();
        $scanner = $scannerFactory->newInstance(
            $visitor = $this->getMockForAbstractClass(Visitor::class)
        );
        self::assertSame($visitor, $scanner->getVisitor());
        self::assertInstanceOf(FilesystemDriver::class, $scanner->getDriver());
        self::assertInstanceOf(BreadthStrategy::class, $scanner->getStrategy());
    }

    public function testNewInstanceWithNodeFactory()
    {
        $scannerFactory = new ScannerFactory();
        $scanner = $scannerFactory->newInstance(
            $visitor = $this->getMockForAbstractClass(Visitor::class)
        );
        self::assertSame($visitor, $scanner->getVisitor());
        self::assertInstanceOf(FilesystemDriver::class, $scanner->getDriver());
        self::assertInstanceOf(BreadthStrategy::class, $scanner->getStrategy());
    }

    public function testNewInstanceWithTargetHandlers()
    {
        $scannerFactory = new ScannerFactory();

        $scannerFactory->setDirectorySecondLevelFilter(
            $nodeTargetHandler = $this->getMockForAbstractClass(SecondLevelFilter::class),
            false);

        $scannerFactory->setFileSecondLevelFilter(
            $leafTargetHandler = $this->getMockForAbstractClass(SecondLevelFilter::class)
        );

        $scanner = $scannerFactory->newInstance(
            $visitor = $this->getMockForAbstractClass(Visitor::class)
        );
        self::assertInstanceOf(ProxyVisitor::class, $proxyVisitor = $scanner->getVisitor());
        self::assertSame($proxyVisitor->extract(), $visitor);

        self::assertInstanceOf(FilesystemDriver::class, $scanner->getDriver());
        self::assertInstanceOf(BreadthStrategy::class, $scanner->getStrategy());


        $reflection = new \ReflectionObject($proxyVisitor);
        $property = $reflection->getProperty('fileSecondLevelFilter');
        $property->setAccessible(true);
        $objectValue = $property->getValue($proxyVisitor);

        self::assertSame($leafTargetHandler, $objectValue);

        $reflection = new \ReflectionObject($proxyVisitor);
        $property = $reflection->getProperty('directorySecondLevelFilter');
        $property->setAccessible(true);
        $objectValue = $property->getValue($proxyVisitor);
        self::assertSame($nodeTargetHandler, $objectValue);

        $reflection = new \ReflectionObject($proxyVisitor);
        $property = $reflection->getProperty('directoryMultiTarget');
        $property->setAccessible(true);
        $objectValue = $property->getValue($proxyVisitor);
        self::assertFalse($objectValue);

        $reflection = new \ReflectionObject($proxyVisitor);
        $property = $reflection->getProperty('fileMultiTarget');
        $property->setAccessible(true);
        $objectValue = $property->getValue($proxyVisitor);
        self::assertTrue($objectValue);
    }

    public function testNewInstanceWithDriver()
    {
        $scannerFactory = new ScannerFactory();
        $scannerFactory->setDriver($driver = $this->getMockForAbstractClass(Driver::class));
        $scanner = $scannerFactory->newInstance(
            $this->getMockForAbstractClass(Visitor::class)
        );
        self::assertSame($scanner->getDriver(), $driver);
    }

    public function testNewInstanceWithLeafFilters()
    {
        $scannerFactory = new ScannerFactory();
        $scannerFactory->setFileFilters([
            $filter1 = $this->getMockForAbstractClass(Filter::class),
            $filter2 = $this->getMockForAbstractClass(Filter::class),
            $filter3 = $this->getMockForAbstractClass(Filter::class),
        ]);

        $scanner = $scannerFactory->newInstance(
            $this->getMockForAbstractClass(Visitor::class)
        );

        $scannerReflection = new \ReflectionObject($scanner);
        $leafVerifierProperty = $scannerReflection->getProperty('leafVerifier');
        $leafVerifierProperty->setAccessible(true);
        $leafVerifierObjectValue = $leafVerifierProperty->getValue($scanner);

        $leafVerifierObjectValueReflection = new \ReflectionObject($leafVerifierObjectValue);
        $initialCheckerProperty = $leafVerifierObjectValueReflection->getProperty('initialChecker');
        $initialCheckerProperty->setAccessible(true);
        $initialCheckerObjectValue = $initialCheckerProperty->getValue($leafVerifierObjectValue);

        $next = $this->assertFilter($initialCheckerObjectValue, $filter1);
        $next = $this->assertFilter($next, $filter2);
        $next = $this->assertFilter($next, $filter3);
        self::assertEmpty($next);
    }

    public function testNewInstanceWithNodeFilters()
    {
        $scannerFactory = new ScannerFactory();
        $scannerFactory->setDirectoryFilters([
            $filter1 = $this->getMockForAbstractClass(Filter::class),
            $filter2 = $this->getMockForAbstractClass(Filter::class),
            $filter3 = $this->getMockForAbstractClass(Filter::class),
        ]);

        $scanner = $scannerFactory->newInstance(
            $this->getMockForAbstractClass(Visitor::class)
        );

        $scannerReflection = new \ReflectionObject($scanner);
        $leafVerifierProperty = $scannerReflection->getProperty('nodeVerifier');
        $leafVerifierProperty->setAccessible(true);
        $leafVerifierObjectValue = $leafVerifierProperty->getValue($scanner);

        $leafVerifierObjectValueReflection = new \ReflectionObject($leafVerifierObjectValue);
        $initialCheckerProperty = $leafVerifierObjectValueReflection->getProperty('initialChecker');
        $initialCheckerProperty->setAccessible(true);
        $initialCheckerObjectValue = $initialCheckerProperty->getValue($leafVerifierObjectValue);

        $next = $this->assertFilter($initialCheckerObjectValue, $filter1);
        $next = $this->assertFilter($next, $filter2);
        $next = $this->assertFilter($next, $filter3);
        self::assertEmpty($next);
    }

}
