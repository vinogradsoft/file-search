<?php
declare(strict_types=1);

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

class TargetHandlerMultiTargetFalseTest extends ProxyVisitorCase
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
                return 'assert';
            }
        };
    }

    public function testVisitLeaf()
    {
        $proxyVisitor = new ProxyVisitor($this, $this->targetHandler, null, false);
        $proxyVisitor->visitLeaf($this->strategy, $this->factory, $this->detect, $this->found);
        self::assertTrue($this->strategy->isStop());
    }

    public function testVisitNode()
    {
        $proxyVisitor = new ProxyVisitor($this, null, $this->targetHandler, true, false);
        $proxyVisitor->visitNode($this->strategy, $this->factory, $this->detect, $this->found);
        self::assertTrue($this->strategy->isStop());
    }

    public function visitLeaf(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect, $found, $data = null): void
    {
        if ($data !== 'assert') {
            self::fail();
        }
        self::assertTrue(true);
    }

    public function visitNode(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect, $found, $data = null): void
    {
        if ($data !== 'assert') {
            self::fail();
        }
        self::assertTrue(true);
    }

    public function tearDown(): void
    {
        $this->strategy = null;
        $this->factory = null;
        $this->detect = null;
        $this->found = null;
    }
}
