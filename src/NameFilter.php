<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

use Vinograd\Scanner\Filter;

class NameFilter implements Filter
{

    private string|null $config = null;

    /**
     * @param mixed $element
     * @return bool
     */
    public function filter(mixed $element): bool
    {
        $name = pathinfo($element, PATHINFO_FILENAME);
        return $this->config === $name;
    }

    /**
     * @param string $config
     */
    public function setConfiguration(string $config): void
    {
        $this->config = $config;
    }

}