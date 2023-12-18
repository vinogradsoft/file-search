<?php
declare(strict_types=1);

namespace Test\Cases;

use PHPUnit\Framework\TestCase;
use Vinograd\Scanner\AbstractTraversalStrategy;
use Vinograd\Scanner\Visitor;

abstract class ProxyVisitorCase extends TestCase implements Visitor
{

    public function scanStarted(AbstractTraversalStrategy $scanStrategy, $detect): void
    {

    }

    public function scanCompleted(AbstractTraversalStrategy $scanStrategy, $detect): void
    {

    }

    public function visitLeaf(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentElement, mixed $data = null): void
    {

    }

    public function visitNode(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentNode, mixed $data = null): void
    {

    }

}