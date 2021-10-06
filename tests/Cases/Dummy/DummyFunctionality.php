<?php

namespace Test\Cases\Dummy;

use Vinograd\IO\Filesystem;
use Vinograd\SimpleFiles\AbstractFilesystemFunctionality;
use Vinograd\Support\Functionality;
use Vinograd\Support\SupportedFunctionalities;

class DummyFunctionality extends AbstractFilesystemFunctionality
{
    private static $self = null;

    /**
     * @param SupportedFunctionalities $component
     */
    protected function installMethods(SupportedFunctionalities $component): void
    {
        $this->assignMethod($component, 'get1');
        $this->assignMethod($component, 'get2');
        $this->assignMethod($component, 'get3');
    }

    /**
     * @param SupportedFunctionalities $component
     */
    protected function uninstallMethods(SupportedFunctionalities $component): void
    {
        $this->revokeMethod($component, 'get1');
        $this->revokeMethod($component, 'get2');
        $this->revokeMethod($component, 'get3');
    }

    public static function create(SupportedFunctionalities $component): Functionality
    {
        if (static::$self === null) {
            static::$self = new self();
            return static::$self;
        }
        return static::$self;
    }

    public function get1(SupportedFunctionalities $component, Filesystem $filesystem, string $dummy)
    {
        return $dummy;
    }

    public function get2(SupportedFunctionalities $component, Filesystem $filesystem)
    {
        return 'get2';
    }

    public function get3(SupportedFunctionalities $component, Filesystem $filesystem)
    {
        return $component->getSource();
    }


    protected function checkArguments($method, $arguments): bool
    {
        if ('get1' === $method) {
            return (count($arguments) === 1 && is_string($arguments[0]));
        }

        if ('get2' === $method) {
            return empty($arguments);
        }

        if ('get3' === $method) {
            return empty($arguments);
        }
        return false;
    }
}