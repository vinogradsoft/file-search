<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

use Vinograd\Scanner\Exception\ConfigurationException;
use Vinograd\Scanner\Filter;

class PrefixFilter implements Filter
{
    /** @var string */
    private $config;

    /**
     * @param string $path
     * @return bool
     */
    public function filter($path): bool
    {
        $sub = substr(basename($path), 0, mb_strlen($this->config));
        return $sub === $this->config;
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