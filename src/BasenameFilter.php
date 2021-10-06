<?php

namespace Vinograd\FileSearch;

use Vinograd\Scanner\Exception\ConfigurationException;
use Vinograd\Scanner\Filter;

class BasenameFilter implements Filter
{
    /** @var string */
    private $config;

    /**
     * @param string $path
     * @return bool
     */
    public function filter($path): bool
    {
        return $this->config === basename($path);
    }

    /**
     * @param string $config
     */
    public function setConfiguration($config): void
    {
        if (!is_string($config)) {
            throw new ConfigurationException('Invalid filter parameter type. String expected.');
        }
        $this->config = $config;
    }
}