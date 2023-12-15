<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

use Vinograd\Scanner\Filter;

class ExtensionFilter implements Filter
{

    private string|null $config = null;

    /**
     * @param mixed $element
     * @return bool
     */
    public function filter(mixed $element): bool
    {
        return $this->config === $this->getExtension($element);
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
    public function setConfiguration(string $config): void
    {
        $this->config = $config;
    }

}