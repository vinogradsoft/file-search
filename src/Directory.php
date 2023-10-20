<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

use Vinograd\Scanner\Node;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class Directory extends AbstractSearchFilesystemObject implements Node
{

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return FileFunctionalitiesContext::fireGlobalDirectoryMethod($this, $method, $args);
    }

}