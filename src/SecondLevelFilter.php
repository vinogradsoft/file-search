<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

interface SecondLevelFilter
{

    /**
     * @param string $parentElement
     * @param string $currentElement
     * @return mixed
     */
    public function execute(string $parentElement, string $currentElement): mixed;

}