<?php

namespace Test\Cases\Dummy;

use Vinograd\Scanner\AbstractTraversalStrategy;
use Vinograd\Scanner\NodeFactory;
use Vinograd\Scanner\Visitor;

class DummyVisitor implements Visitor
{
    private $counter = 0;

    public function scanStarted(AbstractTraversalStrategy $scanStrategy, $detect): void
    {

    }

    public function scanCompleted(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect): void
    {
        echo '______________________________' . PHP_EOL;
        echo 'Найдено файлов: ' . $this->counter . ' шт.' . PHP_EOL;
    }

    public function visitLeaf(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect, $found, $data = null): void
    {
        $this->counter++;
        echo 'Найден файл: ' . $found . PHP_EOL;;
        echo $data . PHP_EOL;
        echo '====================================' . PHP_EOL;
    }

    public function visitNode(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect, $found, $data = null): void
    {

    }

    public function equals(Visitor $visitor): bool
    {
        return $this === $visitor;
    }
}