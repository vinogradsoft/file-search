<?php
declare(strict_types=1);

namespace Test\Unit\ProxyVisitor;

use Test\Cases\ProxyVisitorCase;
use Vinograd\FileSearch\ProxyVisitor;
use Vinograd\Scanner\AbstractTraversalStrategy;
use Vinograd\Scanner\BreadthStrategy;

class EmptySecondLevelFilterTest extends ProxyVisitorCase
{
    private AbstractTraversalStrategy|null $strategy = null;

    private string|null $detect = null;
    private string|null $found = null;

    public function setUp(): void
    {
        $this->strategy = new BreadthStrategy();
        $this->detect = 'detect';
        $this->found = 'found';
    }

    public function testVisitLeaf()
    {
        $proxyVisitor = new ProxyVisitor($this);
        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());
    }

    public function testVisitNode()
    {
        $proxyVisitor = new ProxyVisitor($this);
        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
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
        $proxyVisitor->scanCompleted($this->strategy, $this->detect);
        self::assertFalse($this->strategy->isStop());
    }

    public function scanStarted(AbstractTraversalStrategy $scanStrategy, $detect): void
    {
        self::assertSame($this->strategy, $scanStrategy);
        self::assertEquals($this->detect, $detect);
    }

    public function scanCompleted(AbstractTraversalStrategy $scanStrategy, $detect): void
    {
        self::assertSame($this->strategy, $scanStrategy);
        self::assertEquals($this->detect, $detect);
    }

    public function visitLeaf(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentElement, mixed $data = null): void
    {
        self::assertSame($this->strategy, $scanStrategy);
        self::assertEquals($this->detect, $parentNode);
        self::assertEquals($this->found, $currentElement);
    }

    public function visitNode(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentNode, mixed $data = null): void
    {
        self::assertSame($this->strategy, $scanStrategy);
        self::assertEquals($this->detect, $parentNode);
        self::assertEquals($this->found, $currentNode);
    }

    public function tearDown(): void
    {
        $this->strategy = null;
        $this->detect = null;
        $this->found = null;
    }
}
