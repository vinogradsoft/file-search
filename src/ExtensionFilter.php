<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

class ExtensionFilter extends AbstractFilter
{

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

}