<?php
declare(strict_types=1);

namespace Test\Unit\ProxyVisitor;

use Test\Cases\ProxyVisitorCase;
use Vinograd\FileSearch\ProxyVisitor;
use Vinograd\FileSearch\SecondLevelFilter;
use Vinograd\Scanner\AbstractTraversalStrategy;
use Vinograd\Scanner\BreadthStrategy;

class SecondLevelFilterMultiTargetFalseTest extends ProxyVisitorCase
{
    private $strategy;
    private $detect;
    private $found;
    private $secondLevelFilter;

    public function setUp(): void
    {
        $this->strategy = new BreadthStrategy();
        $this->detect = 'detect';
        $this->found = 'found';
        $this->secondLevelFilter = new class() implements SecondLevelFilter {
            public function execute(string $parentElement, string $currentElement): string
            {
                return 'assert';
            }
        };
    }

    public function testVisitLeaf()
    {
        $proxyVisitor = new ProxyVisitor($this, $this->secondLevelFilter, null, false);
        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());

        $proxyVisitor = new ProxyVisitor($this, $this->secondLevelFilter, null, true);
        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());

        $proxyVisitor = new ProxyVisitor(new class() extends ProxyVisitorCase {
            public function visitNode(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentNode, mixed $data = null): void
            {
                if ($data !== 'assert') {
                    self::fail();
                }
                self::assertTrue(true);
            }
        }, null, $this->secondLevelFilter, null, true);
        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());


        $proxyVisitor = new ProxyVisitor(new class() extends ProxyVisitorCase {
            public function visitNode(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentNode, mixed $data = null): void
            {
                if ($data !== 'assert') {
                    self::fail();
                }
                self::assertTrue(true);
            }
        }, null, $this->secondLevelFilter, null, false);
        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());


        $proxyVisitor = new ProxyVisitor(new class() extends ProxyVisitorCase {
        },
            $this->secondLevelFilter, $this->secondLevelFilter, true, false);

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());


        $proxyVisitor = new ProxyVisitor(new class() extends ProxyVisitorCase {
        },
            $this->secondLevelFilter, $this->secondLevelFilter, false, true);

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());


        $proxyVisitor = new ProxyVisitor(new class() extends ProxyVisitorCase {
        },
            $this->secondLevelFilter, $this->secondLevelFilter, false, false);

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertTrue($this->strategy->isStop());

        $this->strategy->setStop(false);



        $proxyVisitor = new ProxyVisitor(new class() extends ProxyVisitorCase {
        },
            $this->secondLevelFilter, $this->secondLevelFilter, true, true);

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());


        $secondLevelFilter = new class() implements SecondLevelFilter {
            private int $counter = 0;

            public function execute(string $parentElement, string $currentElement): ?string
            {
                $this->counter++;
                if ($this->counter === 3) {
                    return 'bingo';
                }
                return null;
            }

            public function reset()
            {
                $this->counter = 0;
            }
        };

        $proxyVisitor = new ProxyVisitor(new class() extends ProxyVisitorCase {

        },
            $this->secondLevelFilter, $secondLevelFilter, false, false);

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertTrue($this->strategy->isStop());


        $this->strategy->setStop(false);
        $secondLevelFilter->reset();

        $proxyVisitor = new ProxyVisitor(new class() extends ProxyVisitorCase {

        },
            $this->secondLevelFilter, $secondLevelFilter, false, false);

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());

        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());

        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());

        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertTrue($this->strategy->isStop());

        $this->strategy->setStop(false);
        $secondLevelFilter->reset();


        $proxyVisitor = new ProxyVisitor(new class() extends ProxyVisitorCase {

        },
            $secondLevelFilter, $this->secondLevelFilter, false, false);

        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);

        self::assertFalse($this->strategy->isStop());

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        self::assertTrue($this->strategy->isStop());


        $this->strategy->setStop(false);
        $secondLevelFilter->reset();


        $proxyVisitor = new ProxyVisitor(new class() extends ProxyVisitorCase {

        },
            $secondLevelFilter, $this->secondLevelFilter, false, false);

        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);

        self::assertFalse($this->strategy->isStop());

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());

        $proxyVisitor->visitLeaf($this->strategy, $this->detect, $this->found);
        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertTrue($this->strategy->isStop());


    }

    public function testVisitNode()
    {
        $proxyVisitor = new ProxyVisitor($this, null, $this->secondLevelFilter, null, false);
        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());

        $proxyVisitor = new ProxyVisitor($this, null, $this->secondLevelFilter, null, true);
        $proxyVisitor->visitNode($this->strategy, $this->detect, $this->found);
        self::assertFalse($this->strategy->isStop());
    }

    public function visitLeaf(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentElement, mixed $data = null): void
    {
        if ($data !== 'assert') {
            self::fail();
        }
        self::assertTrue(true);
    }

    public function visitNode(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentNode, mixed $data = null): void
    {
        if ($data !== 'assert') {
            self::fail();
        }
        self::assertTrue(true);
    }

    public function tearDown(): void
    {
        $this->strategy = null;
        $this->detect = null;
        $this->found = null;
    }
}
