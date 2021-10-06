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

class TargetHandlerEmptyTargetTest extends ProxyVisitorCase
{
    private $strategy;
    private $factory;
    private $detect;
    private $found;
    private $targetHandler;

    public function setUp(): void
    {
        $this->strategy = new BreadthStrategy();
        $this->factory = new DefaultNodeFactory();
        $this->detect = 'detect';
        $this->found = 'found';
        $this->targetHandler = new class() implements TargetHandler {
            public function execute(NodeFactory $factory, string $detect, string $found)
            {
            }
        };
    }

    public function testVisitLeaf()
    {
        $proxyVisitor = new ProxyVisitor($this, $this->targetHandler);
        $proxyVisitor->visitLeaf($this->strategy, $this->factory, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());
    }

    public function testVisitNode()
    {
        $proxyVisitor = new ProxyVisitor($this, null, $this->targetHandler);
        $proxyVisitor->visitNode($this->strategy, $this->factory, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());
    }

    public function visitLeaf(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect, $found, $data = null): void
    {
        self::fail();
    }

    public function visitNode(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect, $found, $data = null): void
    {
        self::fail();
    }

    public function tearDown(): void
    {
        $this->strategy = null;
        $this->factory = null;
        $this->detect = null;
        $this->found = null;
    }
}
