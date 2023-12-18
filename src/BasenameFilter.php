<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

class BasenameFilter extends AbstractFilter
{

    /**
     * @inheritDoc
     */
    public function filter(mixed $element): bool
    {
        return $this->config === basename($element);
    }

}