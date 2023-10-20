<?php
declare(strict_types=1);

namespace Vinograd\FileSearch;

use Vinograd\Scanner\AbstractTraversalStrategy;
use Vinograd\Scanner\NodeFactory;
use Vinograd\Scanner\Visitor;

class ProxyVisitor implements Visitor
{
    /** @var TargetHandler|null */
    protected $leafHandler = null;

    /** @var TargetHandler|null */
    protected $nodeHandler = null;

    /** @var Visitor|null */
    protected $realVisitor = null;

    /** @var bool */
    protected $leafMultiTarget = true;

    /** @var bool */
    protected $nodeMultiTarget = true;

    public function __construct(
        Visitor        $realVisitor,
        ?TargetHandler $leafHandler = null,
        ?TargetHandler $nodeHandler = null,
        bool           $leafMultiTarget = true,
        bool           $nodeMultiTarget = true
    )
    {
        $this->leafHandler = $leafHandler;
        $this->nodeHandler = $nodeHandler;
        $this->realVisitor = $realVisitor;
        $this->leafMultiTarget = $leafMultiTarget;
        $this->nodeMultiTarget = $nodeMultiTarget;
    }

    /**
     * @param AbstractTraversalStrategy $scanStrategy
     * @param $detect
     */
    public function scanStarted(AbstractTraversalStrategy $scanStrategy, $detect): void
    {
        $this->realVisitor->scanStarted($scanStrategy, $detect);
    }

    /**
     * @param AbstractTraversalStrategy $scanStrategy
     * @param NodeFactory $factory
     * @param $detect
     */
    public function scanCompleted(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect): void
    {
        $this->realVisitor->scanCompleted($scanStrategy, $factory, $detect);
    }

    /**
     * @param AbstractTraversalStrategy $scanStrategy
     * @param NodeFactory $factory
     * @param $detect
     * @param $found
     * @param null $data
     */
    public function visitLeaf(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect, $found, $data = null): void
    {
        if ($this->leafHandler === null) {
            $this->realVisitor->visitLeaf($scanStrategy, $factory, $detect, $found, $data);
            return;
        }
        $result = $this->leafHandler->execute($factory, $detect, $found);
        if ($result !== null) {
            $this->realVisitor->visitLeaf($scanStrategy, $factory, $detect, $found, $result);
            $scanStrategy->setStop(!$this->leafMultiTarget);
        }
    }

    /**
     * @param AbstractTraversalStrategy $scanStrategy
     * @param NodeFactory $factory
     * @param $detect
     * @param $found
     * @param null $data
     */
    public function visitNode(AbstractTraversalStrategy $scanStrategy, NodeFactory $factory, $detect, $found, $data = null): void
    {
        if ($this->nodeHandler === null) {
            $this->realVisitor->visitNode($scanStrategy, $factory, $detect, $found, $data);
            return;
        }
        $result = $this->nodeHandler->execute($factory, $detect, $found);
        if ($result !== null) {
            $this->realVisitor->visitNode($scanStrategy, $factory, $detect, $found, $result);
            $scanStrategy->setStop(!$this->nodeMultiTarget);
        }
    }

    public function clear()
    {
        $this->leafHandler = null;
        $this->nodeHandler = null;
        $this->realVisitor = null;
        $this->nodeMultiTarget = true;
        $this->leafMultiTarget = true;
    }

    /**
     * @param TargetHandler|null $leafHandler
     * @param TargetHandler|null $nodeHandler
     * @param bool $nodeMultiTarget
     * @param bool $leafMultiTarget
     */
    public function update(
        ?TargetHandler $leafHandler = null,
        ?TargetHandler $nodeHandler = null,
        bool           $nodeMultiTarget = true,
        bool           $leafMultiTarget = true
    ): void
    {
        $this->leafHandler = $leafHandler;
        $this->nodeHandler = $nodeHandler;
        $this->nodeMultiTarget = $nodeMultiTarget;
        $this->leafMultiTarget = $leafMultiTarget;
    }

    /**
     * @return Visitor
     */
    public function extract(): Visitor
    {
        return $this->realVisitor;
    }

    public function __destruct()
    {
        $this->clear();
    }

    /**
     * @param Visitor $visitor
     * @return bool
     */
    public function equals(Visitor $visitor): bool
    {
        return $this->realVisitor->equals($visitor);
    }
}