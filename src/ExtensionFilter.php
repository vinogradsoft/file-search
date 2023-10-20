<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

use Vinograd\Scanner\Exception\ConfigurationException;
use Vinograd\Scanner\Filter;

class ExtensionFilter implements Filter
{
    /** @var string */
    private $config;

    /**
     * @param string $path
     * @return bool
     */
    public function filter($path): bool
    {
        return $this->config === $this->getExtension($path);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getExtension(string $path): string
    {
        $name = basename($path);
        $n = strrpos($name, ".");
        return ($n === false) ? "" : substr($name, $n + 1);
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