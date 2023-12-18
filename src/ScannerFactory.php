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
        $scanner->setStrategy($this->strategy());
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
     * @return $this
     */
    public function setDriver(Driver $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @return array<Filter>|null
     */
    public function getFileFilters(): ?array
    {
        return $this->fileFilters;
    }

    /**
     * @param array<Filter> $filters
     * @return $this
     */
    public function setFileFilters(array $filters): self
    {
        $this->fileFilters = $filters;
        return $this;
    }

    /**
     * @return array<Filter>|null
     */
    public function getDirectoryFilters(): ?array
    {
        return $this->directoryFilters;
    }

    /**
     * @param array<Filter> $filters
     * @return $this
     */
    public function setDirectoryFilters(array $filters): self
    {
        $this->directoryFilters = $filters;
        return $this;
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
     * @return ScannerFactory
     */
    public function setDirectorySecondLevelFilter(SecondLevelFilter $directorySecondLevelFilter, bool $directoryMultiTarget): self
    {
        $this->directorySecondLevelFilter = $directorySecondLevelFilter;
        $this->directoryMultiTarget = $directoryMultiTarget;
        return $this;
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
     * @return ScannerFactory
     */
    public function setFileSecondLevelFilter(SecondLevelFilter $fileSecondLevelFilter, bool $fileMultiTarget): self
    {
        $this->fileSecondLevelFilter = $fileSecondLevelFilter;
        $this->fileMultiTarget = $fileMultiTarget;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFileMultiTarget(): bool
    {
        return $this->fileMultiTarget;
    }

    /**
     * @return bool
     */
    public function isDirectoryMultiTarget(): bool
    {
        return $this->directoryMultiTarget;
    }

    /**
     * @param Visitor $visitor
     * @return $this
     */
    protected function setVisitor(Visitor $visitor): self
    {
        $this->visitor = $visitor;
        return $this;
    }

}