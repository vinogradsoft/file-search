<?php

namespace Test\Cases;

use PHPUnit\Framework\TestCase;
use Vinograd\Scanner\AbstractTraversalStrategy;
use Vinograd\Scanner\NodeFactory;
use Vinograd\Scanner\Visitor;

abstract class ProxyVisitorCase extends TestCase implements Visitor
{

    public function scanStarted(AbstractTraversalStrategy $scanStrategy, $detect): void
    {

    }

    public function scanCompleted(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect): void
    {

    }

    public function visitLeaf(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect, $found, $data = null): void
    {

    }

    public function visitNode(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect, $found, $data = null): void
    {

    }

    public function equals(Visitor $visitor): bool
    {
        return $this === $visitor;
    }
}