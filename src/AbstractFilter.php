<?php

namespace Vinograd\FileSearch;

use Vinograd\Scanner\Filter;

abstract class AbstractFilter implements Filter
{

    protected string|null $config = null;

    /**
     * @param string $config
     */
    public function setConfiguration(string $config): void
    {
        $this->config = $config;
    }

}