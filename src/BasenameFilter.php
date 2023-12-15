<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

use Vinograd\Scanner\Filter;

class BasenameFilter implements Filter
{

    private string|null $config;

    /**
     * @inheritDoc
     */
    public function filter(mixed $element): bool
    {
        return $this->config === basename($element);
    }

    /**
     * @param string $config
     */
    public function setConfiguration(string $config): void
    {
        $this->config = $config;
    }

}