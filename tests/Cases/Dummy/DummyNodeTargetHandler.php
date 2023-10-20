<?php
declare(strict_types=1);

namespace Test\Cases\Dummy;

use Vinograd\FileSearch\TargetHandler;
use Vinograd\Scanner\NodeFactory;

class DummyNodeTargetHandler implements TargetHandler
{

    public function execute(NodeFactory $factory, string $detect, string $found)
    {

    }
}