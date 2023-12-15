<?php
declare(strict_types=1);

namespace Test\Unit\ProxyVisitor;

use Test\Cases\ProxyVisitorCase;
use Vinograd\FileSearch\ProxyVisitor;
use Vinograd\FileSearch\SecondLevelFilter;
use Vinograd\Scanner\AbstractTraversalStrategy;
use Vinograd\Scanner\BreadthStrategy;

class SecondLevelFilterEmptyTargetMultiTargetFalseTest extends ProxyVisitorCase
{
    private $strategy;
    private $detect;
    private $found;
    private $targetHandler;

    public function setUp(): void
    {
        $this->strategy = new BreadthStrategy();

        $this->detect = 'detect';
        $this->found = 'found';
        $this->targetHandler = new class() implements SecondLevelFilter {
            public function execute(string $parentElement, string $currentElement): mixed
            {
                return null;
            }
        };
    }

    public function testVisitLeaf()
    {
        $proxyVisitor = new ProxyVisitor($this, $this->targetHandler, null, false);
        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());
    }

    public function testVisitNode()
    {
        $proxyVisitor = new ProxyVisitor($this, null, $this->targetHandler, true, false);
        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());
    }

    public function visitLeaf(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentElement, mixed $data = null): void
    {
        self::fail();
    }

    public function visitNode(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentNode, mixed $data = null): void
    {
        self::fail();
    }

    public function tearDown(): void
    {
        $this->strategy = null;
        $this->detect = null;
        $this->found = null;
    }
}
