<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

use Vinograd\Scanner\Filter;

class PrefixFilter implements Filter
{

    private string|null $config;

    /**
     * @param mixed $element
     * @return bool
     */
    public function filter(mixed $element): bool
    {
        $sub = substr(basename($element), 0, mb_strlen($this->config));
        return $sub === $this->config;
    }

    /**
     * @param string $config
     */
    public function setConfiguration(string $config): void
    {
        $this->config = $config;
    }

}