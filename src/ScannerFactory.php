<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

use Vinograd\FilesDriver\FilesystemDriver;
use Vinograd\Scanner\AbstractTraversalStrategy;
use Vinograd\Scanner\BreadthStrategy;
use Vinograd\Scanner\Driver;
use Vinograd\Scanner\Filter;
use Vinograd\Scanner\NodeFactory;
use Vinograd\Scanner\Scanner;
use Vinograd\Scanner\Visitor;

class ScannerFactory
{
    /** @var Driver */
    protected $driver;

    /** @var AbstractTraversalStrategy */
    protected $strategy;

    /** @var NodeFactory */
    protected $nodeFactory;

    /** @var Filter[] */
    protected $leafFilters;

    /** @var Filter[] */
    protected $nodeFilters;

    /** @var TargetHandler */
    protected $nodeTargetHandler;

    /** @var TargetHandler */
    protected $leafTargetHandler;

    /** @var boolean */
    protected $leafMultiTarget = true;

    /** @var boolean */
    protected $nodeMultiTarget = true;

    /** @var Visitor */
    protected $visitor;

    /**
     * @param Visitor $visitor
     * @param NodeFactory|null $nodeFactory
     * @return Scanner
     */
    public function newInstance(Visitor $visitor, ?NodeFactory $nodeFactory = null): Scanner
    {
        if (!empty($nodeFactory)) {
            $this->setNodeFactory($nodeFactory);
        }
        $this->setVisitor($visitor);
        $scanner = new Scanner();
        $this->installVisitor($scanner);
        $this->installNodeFactory($scanner);
        $this->installDriver($scanner);
        $this->installStrategy($scanner);
        $this->installLeafFilters($scanner);
        $this->installNodeFilters($scanner);

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
     * @return NodeFactory
     */
    protected function nodeFactory(): NodeFactory
    {
        return new DefaultNodeFactory();
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
    protected function installNodeFactory(Scanner $scanner): void
    {
        if (empty($this->nodeFactory)) {
            $scanner->setNodeFactory($this->nodeFactory());
            return;
        }
        $scanner->setNodeFactory($this->nodeFactory);
    }

    /**
     * @param Scanner $scanner
     */
    protected function installLeafFilters(Scanner $scanner): void
    {
        if (empty($this->leafFilters)) {
            return;
        }
        foreach ($this->leafFilters as $filter) {
            $scanner->addLeafFilter($filter);
        }
    }

    /**
     * @param Scanner $scanner
     */
    protected function installNodeFilters(Scanner $scanner): void
    {
        if (empty($this->nodeFilters)) {
            return;
        }
        foreach ($this->nodeFilters as $filter) {
            $scanner->addNodeFilter($filter);
        }
    }

    /**
     * @param Scanner $scanner
     */
    protected function installVisitor(Scanner $scanner): void
    {
        if (!empty($this->nodeTargetHandler) || !empty($this->leafTargetHandler)) {
            $scanner->setVisitor(new ProxyVisitor(
                $this->visitor,
                $this->leafTargetHandler,
                $this->nodeTargetHandler,
                $this->leafMultiTarget,
                $this->nodeMultiTarget
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
     * @return NodeFactory|null
     */
    public function getNodeFactory(): ?NodeFactory
    {
        return $this->nodeFactory;
    }

    /**
     * @param NodeFactory $nodeFactory
     */
    public function setNodeFactory(NodeFactory $nodeFactory): void
    {
        $this->nodeFactory = $nodeFactory;
    }

    /**
     * @return Filter[]|null
     */
    public function getLeafFilters(): ?array
    {
        return $this->leafFilters;
    }

    /**
     * @param Filter[] $leafFilters
     */
    public function setLeafFilters(array $leafFilters): void
    {
        $this->leafFilters = $leafFilters;
    }

    /**
     * @return Filter[]|null
     */
    public function getNodeFilters(): ?array
    {
        return $this->nodeFilters;
    }

    /**
     * @param Filter[] $nodeFilters
     */
    public function setNodeFilters(array $nodeFilters): void
    {
        $this->nodeFilters = $nodeFilters;
    }

    /**
     * @return TargetHandler|null
     */
    public function getNodeTargetHandler(): ?TargetHandler
    {
        return $this->nodeTargetHandler;
    }

    /**
     * @param TargetHandler $nodeTargetHandler
     * @param bool $nodeMultiTarget
     */
    public function setNodeTargetHandler(TargetHandler $nodeTargetHandler, bool $nodeMultiTarget = true): void
    {
        $this->nodeTargetHandler = $nodeTargetHandler;
        $this->setNodeMultiTarget($nodeMultiTarget);
    }

    /**
     * @return TargetHandler|null
     */
    public function getLeafTargetHandler(): ?TargetHandler
    {
        return $this->leafTargetHandler;
    }

    /**
     * @param TargetHandler $leafTargetHandler
     * @param bool $leafMultiTarget
     */
    public function setLeafTargetHandler(TargetHandler $leafTargetHandler, bool $leafMultiTarget = true): void
    {
        $this->leafTargetHandler = $leafTargetHandler;
        $this->setLeafMultiTarget($leafMultiTarget);
    }

    /**
     * @return bool
     */
    public function isLeafMultiTarget(): bool
    {
        return $this->leafMultiTarget;
    }

    /**
     * @param bool $leafMultiTarget
     */
    public function setLeafMultiTarget(bool $leafMultiTarget): void
    {
        $this->leafMultiTarget = $leafMultiTarget;
    }

    /**
     * @return bool
     */
    public function isNodeMultiTarget(): bool
    {
        return $this->nodeMultiTarget;
    }

    /**
     * @param bool $nodeMultiTarget
     */
    public function setNodeMultiTarget(bool $nodeMultiTarget): void
    {
        $this->nodeMultiTarget = $nodeMultiTarget;
    }

    /**
     * @param Visitor $visitor
     */
    protected function setVisitor(Visitor $visitor): void
    {
        $this->visitor = $visitor;
    }

}