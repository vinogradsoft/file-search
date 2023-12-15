<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

use Vinograd\FilesDriver\FilesystemDriver;
use Vinograd\Scanner\AbstractTraversalStrategy;
use Vinograd\Scanner\BreadthStrategy;
use Vinograd\Scanner\Driver;
use Vinograd\Scanner\Filter;
use Vinograd\Scanner\Scanner;
use Vinograd\Scanner\Visitor;

class ScannerFactory
{

    protected Driver|null $driver = null;

    protected AbstractTraversalStrategy $strategy;

    /** @var array<Filter> */
    protected array|null $fileFilters = null;

    /** @var array<Filter> */
    protected array|null $directoryFilters = null;

    protected SecondLevelFilter|null $directorySecondLevelFilter = null;

    protected SecondLevelFilter|null $fileSecondLevelFilter = null;

    protected bool $fileMultiTarget = true;

    protected bool $directoryMultiTarget = true;

    protected Visitor $visitor;

    /**
     * @param Visitor $visitor
     * @return Scanner
     */
    public function newInstance(Visitor $visitor): Scanner
    {
        $this->setVisitor($visitor);
        $scanner = new Scanner();
        $this->installVisitor($scanner);
        $this->installDriver($scanner);
        $this->installStrategy($scanner);
        $this->installFileFilters($scanner);
        $this->installDirectoryFilters($scanner);

        return $scanner;
    }

    /**
     * @return Driver
     */
    protected function driver(): Driver
    {
        return new FilesystemDriver();
    }

    /**
     * @return AbstractTraversalStrategy
     */
    protected function strategy(): AbstractTraversalStrategy
    {
        return new BreadthStrategy();
    }

    /**
     * @param Scanner $scanner
     */
    protected function installDriver(Scanner $scanner): void
    {
        if (empty($this->driver)) {
            $scanner->setDriver($this->driver());
            return;
        }
        $scanner->setDriver($this->driver);
    }

    /**
     * @param Scanner $scanner
     */
    protected function installStrategy(Scanner $scanner): void
    {
        if (empty($this->strategy)) {
            $scanner->setStrategy($this->strategy());
            return;
        }
        $scanner->setStrategy($this->strategy);
    }

    /**
     * @param Scanner $scanner
     */
    protected function installFileFilters(Scanner $scanner): void
    {
        if (empty($this->fileFilters)) {
            return;
        }
        foreach ($this->fileFilters as $filter) {
            $scanner->addLeafFilter($filter);
        }
    }

    /**
     * @param Scanner $scanner
     */
    protected function installDirectoryFilters(Scanner $scanner): void
    {
        if (empty($this->directoryFilters)) {
            return;
        }
        foreach ($this->directoryFilters as $filter) {
            $scanner->addNodeFilter($filter);
        }
    }

    /**
     * @param Scanner $scanner
     */
    protected function installVisitor(Scanner $scanner): void
    {
        if (!empty($this->directorySecondLevelFilter) || !empty($this->fileSecondLevelFilter)) {
            $scanner->setVisitor(new ProxyVisitor(
                $this->visitor,
                $this->fileSecondLevelFilter,
                $this->directorySecondLevelFilter,
                $this->fileMultiTarget,
                $this->directoryMultiTarget
            ));
            return;
        }
        $scanner->setVisitor($this->visitor);
    }

    /**
     * @return Driver|null
     */
    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    /**
     * @param Driver $driver
     */
    public function setDriver(Driver $driver): void
    {
        $this->driver = $driver;
    }

    /**
     * @return AbstractTraversalStrategy|null
     */
    public function getStrategy(): ?AbstractTraversalStrategy
    {
        return $this->strategy;
    }

    /**
     * @param AbstractTraversalStrategy $strategy
     */
    public function setStrategy(AbstractTraversalStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    /**
     * @return Filter[]|null
     */
    public function getFileFilters(): ?array
    {
        return $this->fileFilters;
    }

    /**
     * @param Filter[] $filters
     */
    public function setFileFilters(array $filters): void
    {
        $this->fileFilters = $filters;
    }

    /**
     * @return Filter[]|null
     */
    public function getDirectoryFilters(): ?array
    {
        return $this->directoryFilters;
    }

    /**
     * @param Filter[] $filters
     * @return void
     */
    public function setDirectoryFilters(array $filters): void
    {
        $this->directoryFilters = $filters;
    }

    /**
     * @return SecondLevelFilter|null
     */
    public function getDirectorySecondLevelFilter(): ?SecondLevelFilter
    {
        return $this->directorySecondLevelFilter;
    }

    /**
     * @param SecondLevelFilter $directorySecondLevelFilter
     * @param bool $directoryMultiTarget
     */
    public function setDirectorySecondLevelFilter(SecondLevelFilter $directorySecondLevelFilter, bool $directoryMultiTarget = true): void
    {
        $this->directorySecondLevelFilter = $directorySecondLevelFilter;
        $this->setDirectoryMultiTarget($directoryMultiTarget);
    }

    /**
     * @return SecondLevelFilter|null
     */
    public function getFileSecondLevelFilter(): ?SecondLevelFilter
    {
        return $this->fileSecondLevelFilter;
    }

    /**
     * @param SecondLevelFilter $fileSecondLevelFilter
     * @param bool $fileMultiTarget
     */
    public function setFileSecondLevelFilter(SecondLevelFilter $fileSecondLevelFilter, bool $fileMultiTarget = true): void
    {
        $this->fileSecondLevelFilter = $fileSecondLevelFilter;
        $this->setFileMultiTarget($fileMultiTarget);
    }

    /**
     * @return bool
     */
    public function isFileMultiTarget(): bool
    {
        return $this->fileMultiTarget;
    }

    /**
     * @param bool $fileMultiTarget
     */
    public function setFileMultiTarget(bool $fileMultiTarget): void
    {
        $this->fileMultiTarget = $fileMultiTarget;
    }

    /**
     * @return bool
     */
    public function isDirectoryMultiTarget(): bool
    {
        return $this->directoryMultiTarget;
    }

    /**
     * @param bool $directoryMultiTarget
     */
    public function setDirectoryMultiTarget(bool $directoryMultiTarget): void
    {
        $this->directoryMultiTarget = $directoryMultiTarget;
    }

    /**
     * @param Visitor $visitor
     */
    protected function setVisitor(Visitor $visitor): void
    {
        $this->visitor = $visitor;
    }

}