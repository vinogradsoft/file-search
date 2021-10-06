<?php

namespace Vinograd\FileSearch;

use Vinograd\Scanner\Leaf;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class File extends AbstractSearchFilesystemObject implements Leaf
{

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return FileFunctionalitiesContext::fireGlobalFileMethod($this, $method, $args);
    }

}