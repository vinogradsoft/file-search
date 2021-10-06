<?php

namespace Vinograd\FileSearch;

use Vinograd\SimpleFiles\AbstractFilesystemObject;
use Vinograd\Support\Functionality;

class AbstractSearchFilesystemObject extends AbstractFilesystemObject
{
    /** @var string  */
   protected $path;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->path;
    }

    /**
     * @param $data
     */
    protected function setData($data): void
    {
        $this->path = $data;
    }

    /**
     * @param Functionality $support
     */
    public function assignSupport(Functionality $support): void
    {
        $this->addFunctionality($support);
    }

    /**
     * @param Functionality $support
     */
    public function revokeSupport(Functionality $support): void
    {
        $this->removeFunctionality($support);
    }

}