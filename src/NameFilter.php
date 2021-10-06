<?php

namespace Vinograd\FileSearch;

use Vinograd\Scanner\Exception\ConfigurationException;
use Vinograd\Scanner\Filter;

class NameFilter implements Filter
{
    /** @var string */
    private $config;

    /**
     * @param string $path
     * @return bool
     */
    public function filter($path): bool
    {
        $name = pathinfo($path, PATHINFO_FILENAME);
        return $this->config === $name;
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