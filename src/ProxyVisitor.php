<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

use Vinograd\Scanner\AbstractTraversalStrategy;
use Vinograd\Scanner\Visitor;

class ProxyVisitor implements Visitor
{

    protected SecondLevelFilter|null $fileSecondLevelFilter = null;

    protected SecondLevelFilter|null $directorySecondLevelFilter = null;

    protected Visitor|null $realVisitor = null;

    protected bool $fileMultiTarget = true;

    protected bool $directoryMultiTarget = true;

    protected bool $stopForFiles = false;

    protected bool $stopForDirectories = false;

    /**
     * @param Visitor $realVisitor
     * @param SecondLevelFilter|null $fileSecondLevelFilter
     * @param SecondLevelFilter|null $directorySecondLevelFilter
     * @param bool $fileMultiTarget
     * @param bool $directoryMultiTarget
     */
    public function __construct(
        Visitor            $realVisitor,
        ?SecondLevelFilter $fileSecondLevelFilter = null,
        ?SecondLevelFilter $directorySecondLevelFilter = null,
        ?bool              $fileMultiTarget = null,
        ?bool              $directoryMultiTarget = null
    )
    {
        $this->fileSecondLevelFilter = $fileSecondLevelFilter;
        $this->directorySecondLevelFilter = $directorySecondLevelFilter;
        $this->realVisitor = $realVisitor;
        $this->fileMultiTarget = $fileMultiTarget === null ? true : $fileMultiTarget;
        $this->directoryMultiTarget = $directoryMultiTarget === null ? true : $directoryMultiTarget;
    }

    /**
     * @inheritDoc
     */
    public function scanStarted(AbstractTraversalStrategy $scanStrategy, $detect): void
    {
        $this->realVisitor->scanStarted($scanStrategy, $detect);
    }

    /**
     * @inheritDoc
     */
    public function scanCompleted(AbstractTraversalStrategy $scanStrategy, $detect): void
    {
        $this->realVisitor->scanCompleted($scanStrategy, $detect);
    }

    /**
     * @inheritDoc
     */
    public function visitLeaf(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentElement, mixed $data = null): void
    {
        if ($this->fileSecondLevelFilter === null) {
            $this->realVisitor->visitLeaf($scanStrategy, $parentNode, $currentElement, $data);
            return;
        }

        if ($this->stopForFiles) {
            return;
        }

        if ($this->directoryMultiTarget && $this->fileMultiTarget || !$this->directoryMultiTarget && $this->fileMultiTarget) {
            $result = $this->fileSecondLevelFilter->execute($parentNode, $currentElement);
            if ($result !== null) {
                $this->realVisitor->visitLeaf($scanStrategy, $parentNode, $currentElement, $result);
            }
            return;
        } elseif ($this->directoryMultiTarget && !$this->fileMultiTarget) {
            $result = $this->fileSecondLevelFilter->execute($parentNode, $currentElement);
            if ($result !== null) {
                $this->realVisitor->visitLeaf($scanStrategy, $parentNode, $currentElement, $result);
                $this->stopForFiles = true;
            }
            return;
        }

        $result = $this->fileSecondLevelFilter->execute($parentNode, $currentElement);
        if ($result !== null) {
            $this->realVisitor->visitLeaf($scanStrategy, $parentNode, $currentElement, $result);
            $this->stopForFiles = true;
            $scanStrategy->setStop($this->stopForDirectories);
        }
    }

    /**
     * @inheritDoc
     */
    public function visitNode(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentNode, mixed $data = null): void
    {
        if ($this->directorySecondLevelFilter === null) {
            $this->realVisitor->visitNode($scanStrategy, $parentNode, $currentNode, $data);
            return;
        }

        if ($this->stopForDirectories) {
            return;
        }

        if ($this->directoryMultiTarget && $this->fileMultiTarget || $this->directoryMultiTarget && !$this->fileMultiTarget) {
            $result = $this->directorySecondLevelFilter->execute($parentNode, $currentNode);
            if ($result !== null) {
                $this->realVisitor->visitNode($scanStrategy, $parentNode, $currentNode, $result);
            }
            return;
        } elseif (!$this->directoryMultiTarget && $this->fileMultiTarget) {
            $result = $this->directorySecondLevelFilter->execute($parentNode, $currentNode);
            if ($result !== null) {
                $this->realVisitor->visitNode($scanStrategy, $parentNode, $currentNode, $result);
                $this->stopForDirectories = true;
            }
            return;
        }

        $result = $this->directorySecondLevelFilter->execute($parentNode, $currentNode);
        if ($result !== null) {
            $this->realVisitor->visitNode($scanStrategy, $parentNode, $currentNode, $result);
            $this->stopForDirectories = true;
            $scanStrategy->setStop($this->stopForFiles);
        }
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->fileSecondLevelFilter = null;
        $this->directorySecondLevelFilter = null;
        $this->realVisitor = null;
        $this->directoryMultiTarget = true;
        $this->fileMultiTarget = true;
    }

    /**
     * @param SecondLevelFilter|null $fileSecondLevelFilter
     * @param SecondLevelFilter|null $directorySecondLevelFilter
     * @param bool $directoryMultiTarget
     * @param bool $fileMultiTarget
     */
    public function update(
        ?SecondLevelFilter $fileSecondLevelFilter = null,
        ?SecondLevelFilter $directorySecondLevelFilter = null,
        bool               $directoryMultiTarget = true,
        bool               $fileMultiTarget = true
    ): void
    {
        $this->fileSecondLevelFilter = $fileSecondLevelFilter;
        $this->directorySecondLevelFilter = $directorySecondLevelFilter;
        $this->directoryMultiTarget = $directoryMultiTarget;
        $this->fileMultiTarget = $fileMultiTarget;
    }

    /**
     * @return Visitor
     */
    public function extract(): Visitor
    {
        return $this->realVisitor;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->clear();
    }

}