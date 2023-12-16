<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

class NameFilter extends AbstractFilter
{

    /**
     * @param mixed $element
     * @return bool
     */
    public function filter(mixed $element): bool
    {
        return $this->config === pathinfo($element, PATHINFO_FILENAME);
    }

}