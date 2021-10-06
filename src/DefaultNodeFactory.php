<?php

namespace Vinograd\FileSearch;

use Vinograd\Scanner\Leaf;
use Vinograd\Scanner\Node;
use Vinograd\Scanner\NodeFactory;

class DefaultNodeFactory implements NodeFactory
{
    /** @var File  */
    private $filePrototype;

    /** @var Directory  */
    private $directoryPrototype;

    public function __construct()
    {
        $this->filePrototype = new File('');
        $this->directoryPrototype = new Directory('');
    }

    /**
     * @param $detect
     * @param $found
     * @return Node
     */
    public function createNode($detect, $found): Node
    {
        return $this->directoryPrototype->cloneWithData($detect . DIRECTORY_SEPARATOR . $found);
    }

    /**
     * @param $detect
     * @param $found
     * @return Leaf
     */
    public function createLeaf($detect, $found): Leaf
    {
        return $this->filePrototype->cloneWithData($detect . DIRECTORY_SEPARATOR . $found);
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->filePrototype->revokeAllSupports();
        $this->directoryPrototype->revokeAllSupports();
    }
}