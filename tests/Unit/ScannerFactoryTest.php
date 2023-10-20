<?php
declare(strict_types=1);

namespace Test\Unit;

use Test\Cases\ScannerFactoryCase;
use Vinograd\FilesDriver\FilesystemDriver;
use Vinograd\FileSearch\DefaultNodeFactory;
use Vinograd\FileSearch\ProxyVisitor;
use Vinograd\FileSearch\ScannerFactory;
use Vinograd\FileSearch\TargetHandler;
use Vinograd\Scanner\AbstractTraversalStrategy;
use Vinograd\Scanner\BreadthStrategy;
use Vinograd\Scanner\Driver;
use Vinograd\Scanner\Filter;
use Vinograd\Scanner\NodeFactory;
use Vinograd\Scanner\Visitor;

class ScannerFactoryTest extends ScannerFactoryCase
{

    public function testSetDriver()
    {
        $scannerFactory = new ScannerFactory();
        $scannerFactory->setDriver($driver = $this->getMockForAbstractClass(Driver::class));
        self::assertSame($driver, $scannerFactory->getDriver());
    }

    public function testSetStrategy()
    {
        $scannerFactory = new ScannerFactory();
        $scannerFactory->setStrategy($strategy = $this->getMockForAbstractClass(AbstractTraversalStrategy::class));
        self::assertSame($strategy, $scannerFactory->getStrategy());
    }

    public function testSetNodeFactory()
    {
        $scannerFactory = new ScannerFactory();
        $scannerFactory->setNodeFactory($factory = $this->getMockForAbstractClass(NodeFactory::class));
        self::assertSame($factory, $scannerFactory->getNodeFactory());
    }

    public function testSetLeafFilters()
    {
        $scannerFactory = new ScannerFactory();
        self::assertEmpty($scannerFactory->getLeafFilters());
        $scannerFactory->setLeafFilters([
            $filter1 = $this->getMockForAbstractClass(Filter::class),
            $filter2 = $this->getMockForAbstractClass(Filter::class),
            $filter3 = $this->getMockForAbstractClass(Filter::class),
        ]);
        $filters = $scannerFactory->getLeafFilters();
        self::assertContains($filter1, $filters);
        self::assertContains($filter2, $filters);
        self::assertContains($filter3, $filters);
    }

    public function testSetNodeFilters()
    {
        $scannerFactory = new ScannerFactory();
        self::assertEmpty($scannerFactory->getNodeFilters());
        $scannerFactory->setNodeFilters([
            $filter1 = $this->getMockForAbstractClass(Filter::class),
            $filter2 = $this->getMockForAbstractClass(Filter::class),
            $filter3 = $this->getMockForAbstractClass(Filter::class),
        ]);
        $filters = $scannerFactory->getNodeFilters();
        self::assertContains($filter1, $filters);
        self::assertContains($filter2, $filters);
        self::assertContains($filter3, $filters);
    }

    public function testSetNodeTargetHandler()
    {
        $scannerFactory = new ScannerFactory();
        self::assertTrue($scannerFactory->isNodeMultiTarget());
        $scannerFactory->setNodeTargetHandler(
            $handler = $this->getMockForAbstractClass(TargetHandler::class),
            false
        );
        self::assertFalse($scannerFactory->isNodeMultiTarget());
        self::assertSame($handler, $scannerFactory->getNodeTargetHandler());
    }

    public function testSetLeafTargetHandler()
    {
        $scannerFactory = new ScannerFactory();
        self::assertTrue($scannerFactory->isLeafMultiTarget());
        $scannerFactory->setLeafTargetHandler(
            $handler = $this->getMockForAbstractClass(TargetHandler::class),
            false
        );
        self::assertFalse($scannerFactory->isLeafMultiTarget());
        self::assertSame($handler, $scannerFactory->getLeafTargetHandler());
    }

    public function testNewInstance()
    {
        $scannerFactory = new ScannerFactory();
        $scanner = $scannerFactory->newInstance(
            $visitor = $this->getMockForAbstractClass(Visitor::class)
        );
        self::assertSame($visitor, $scanner->getVisitor());
        self::assertInstanceOf(DefaultNodeFactory::class, $scanner->getNodeFactory());
        self::assertInstanceOf(FilesystemDriver::class, $scanner->getDriver());
        self::assertInstanceOf(BreadthStrategy::class, $scanner->getStrategy());
    }

    public function testNewInstanceWithNodeFactory()
    {
        $scannerFactory = new ScannerFactory();
        $scanner = $scannerFactory->newInstance(
            $visitor = $this->getMockForAbstractClass(Visitor::class),
            $nodeFactory = $this->getMockForAbstractClass(NodeFactory::class)
        );
        self::assertSame($visitor, $scanner->getVisitor());
        self::assertSame($nodeFactory, $scanner->getNodeFactory());
        self::assertInstanceOf(FilesystemDriver::class, $scanner->getDriver());
        self::assertInstanceOf(BreadthStrategy::class, $scanner->getStrategy());
    }

    public function testNewInstanceWithTargetHandlers()
    {
        $scannerFactory = new ScannerFactory();

        $scannerFactory->setNodeTargetHandler(
            $nodeTargetHandler = $this->getMockForAbstractClass(TargetHandler::class),
            false);

        $scannerFactory->setLeafTargetHandler(
            $leafTargetHandler = $this->getMockForAbstractClass(TargetHandler::class)
        );

        $scanner = $scannerFactory->newInstance(
            $visitor = $this->getMockForAbstractClass(Visitor::class),
            $nodeFactory = $this->getMockForAbstractClass(NodeFactory::class)
        );
        self::assertInstanceOf(ProxyVisitor::class, $proxyVisitor = $scanner->getVisitor());
        self::assertSame($proxyVisitor->extract(), $visitor);

        self::assertSame($nodeFactory, $scanner->getNodeFactory());
        self::assertInstanceOf(FilesystemDriver::class, $scanner->getDriver());
        self::assertInstanceOf(BreadthStrategy::class, $scanner->getStrategy());


        $reflection = new \ReflectionObject($proxyVisitor);
        $property = $reflection->getProperty('leafHandler');
        $property->setAccessible(true);
        $objectValue = $property->getValue($proxyVisitor);

        self::assertSame($leafTargetHandler, $objectValue);

        $reflection = new \ReflectionObject($proxyVisitor);
        $property = $reflection->getProperty('nodeHandler');
        $property->setAccessible(true);
        $objectValue = $property->getValue($proxyVisitor);
        self::assertSame($nodeTargetHandler, $objectValue);

        $reflection = new \ReflectionObject($proxyVisitor);
        $property = $reflection->getProperty('nodeMultiTarget');
        $property->setAccessible(true);
        $objectValue = $property->getValue($proxyVisitor);
        self::assertFalse($objectValue);

        $reflection = new \ReflectionObject($proxyVisitor);
        $property = $reflection->getProperty('leafMultiTarget');
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

    public function testNewInstanceWithStrategy()
    {
        $scannerFactory = new ScannerFactory();
        $scannerFactory->setStrategy($strategy = $this->getMockForAbstractClass(AbstractTraversalStrategy::class));
        $scanner = $scannerFactory->newInstance(
            $this->getMockForAbstractClass(Visitor::class)
        );
        self::assertSame($scanner->getStrategy(), $strategy);
    }

    public function testNewInstanceWithLeafFilters()
    {
        $scannerFactory = new ScannerFactory();
        $scannerFactory->setLeafFilters([
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
        $scannerFactory->setNodeFilters([
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
