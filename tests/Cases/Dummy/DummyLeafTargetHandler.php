<?php

namespace Test\Cases\Dummy;

use Vinograd\FileSearch\TargetHandler;
use Vinograd\Scanner\NodeFactory;

class DummyLeafTargetHandler implements TargetHandler
{

    public function execute(NodeFactory $factory, string $detect, string $found)
    {
        $file = $factory->createLeaf($detect, $found);
        $yml = $file->yamlParseFile();
        $file->revokeAllSupports();
        if (isset($yml['version'])) {
            return 'Значение версии равно ' . $yml['version'];
        }
        return null;
    }

}