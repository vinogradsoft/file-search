<?php

namespace Test\Unit\ProxyVisitor;

use Test\Cases\Dummy\DummyLeafTargetHandler;
use Test\Cases\Dummy\DummyNodeTargetHandler;
use Test\Cases\Dummy\DummyVisitor;
use Test\Cases\IoEnvCase;
use Test\Cases\ProxyVisitorCase;
use Vinograd\FilesDriver\FilesystemDriver;
use Vinograd\FileSearch\DefaultNodeFactory;
use Vinograd\FileSearch\ExtensionFilter;
use Vinograd\FileSearch\ProxyVisitor;
use PHPUnit\Framework\TestCase;
use Vinograd\FileSearch\ScannerFactory;
use Vinograd\FileSearch\TargetHandler;
use Vinograd\Scanner\AbstractTraversalStrategy;
use Vinograd\Scanner\BreadthStrategy;
use Vinograd\Scanner\NodeFactory;
use Vinograd\Scanner\Scanner;
use Vinograd\Scanner\Visitor;

class EmptyTargetHandlerTest extends ProxyVisitorCase
{
    private $strategy;
    private $factory;
    private $detect;
    private $found;

    public function setUp(): void
    {
        $this->strategy = new BreadthStrategy();
        $this->factory = new DefaultNodeFactory();
        $this->detect = 'detect';
        $this->found = 'found';
    }

    public function testVisitLeaf()
    {
        $proxyVisitor = new ProxyVisitor($this);
        $proxyVisitor->visitLeaf($this->strategy, $this->factory, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());
    }

    public function testVisitNode()
    {
        $proxyVisitor = new ProxyVisitor($this);
        $proxyVisitor->visitNode($this->strategy, $this->factory, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());
    }

    public function testScanStarted()
    {
        $proxyVisitor = new ProxyVisitor($this);
        $proxyVisitor->scanStarted($this->strategy, $this->detect);
        self::assertFalse($this->strategy->isStop());
    }

    public function testScanCompleted()
    {
        $proxyVisitor = new ProxyVisitor($this);
        $proxyVisitor->scanCompleted($this->strategy, $this->factory, $this->detect);
        self::assertFalse($this->strategy->isStop());
    }

    public function scanStarted(AbstractTraversalStrategy $scanStrategy, $detect): void
    {
        self::assertSame($this->strategy, $scanStrategy);
        self::assertEquals($this->detect, $detect);
    }

    public function scanCompleted(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect): void
    {
        self::assertSame($this->strategy, $scanStrategy);
        self::assertSame($this->factory, $factory);
        self::assertEquals($this->detect, $detect);
    }

    public function visitLeaf(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect, $found, $data = null): void
    {
        self::assertSame($this->strategy, $scanStrategy);
        self::assertSame($this->factory, $factory);
        self::assertEquals($this->detect, $detect);
        self::assertEquals($this->found, $found);
    }

    public function visitNode(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect, $found, $data = null): void
    {
        self::assertSame($this->strategy, $scanStrategy);
        self::assertSame($this->factory, $factory);
        self::assertEquals($this->detect, $detect);
        self::assertEquals($this->found, $found);
    }

    public function tearDown(): void
    {
        $this->strategy = null;
        $this->factory = null;
        $this->detect = null;
        $this->found = null;
    }
}
