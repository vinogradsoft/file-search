<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

class PrefixFilter extends AbstractFilter
{

    /**
     * @param mixed $element
     * @return bool
     */
    public function filter(mixed $element): bool
    {
        return substr(basename($element), 0, mb_strlen($this->config)) === $this->config;
    }

}