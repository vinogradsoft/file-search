<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

use Vinograd\Scanner\NodeFactory;

interface TargetHandler
{
    /**
     * @param NodeFactory $factory
     * @param string $detect
     * @param string $found
     * @return mixed|null
     */
    public function execute(NodeFactory $factory, string $detect, string $found);
}