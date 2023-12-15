<?php
declare(strict_types=1);

namespace Test\Unit;

use Test\Cases\Dummy\TestCaseProviderVisitor;
use Test\Cases\StrategyCase;
use Vinograd\FileSearch\ScannerFactory;
use Vinograd\FileSearch\SecondLevelFilter;
use Vinograd\Scanner\AbstractTraversalStrategy;

class ScannerFactorySearchTest extends StrategyCase
{

    private $leafCounter = 0;
    private $nodeCounter = 0;
    private $nodeLog = [];
    private $leafLog = [];
    private $visitor;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->visitor = new TestCaseProviderVisitor($this);

        $this->createFilesystem([
            'directories' => [
                $this->outPath . '/childL',
                $this->outPath . '/childL/root',
                $this->outPath . '/childL/root/child1',
                $this->outPath . '/childL/root/child1/child2',
                $this->outPath . '/childL/root/child1/child2/child3',
                $this->outPath . '/childL/root/child1/child2/child3/child4',
                $this->outPath . '/childL/root/child1/child2/child3/child4/child5',
            ],
            'files' => [
                $this->outPath . '/childL/file1.txt' => 'initial1',
                $this->outPath . '/childL/root/file7.txt' => 'initial7',
                $this->outPath . '/childL/root/child1/file6.txt' => 'initial6',
                $this->outPath . '/childL/root/child1/file7.txt' => 'initial7_d',
                $this->outPath . '/childL/root/child1/child2/file5.txt' => 'initial5',
                $this->outPath . '/childL/root/child1/child2/child3/file4.txt' => 'initial4',
                $this->outPath . '/childL/root/child1/child2/child3/child4/file3.txt' => 'initial3',
                $this->outPath . '/childL/root/child1/child2/child3/child4/child5/file2.txt' => 'initial2',
            ],
        ]);
    }

    /**
     * @dataProvider getCase
     */
    public function testSearch($expectedFiles, $expectedDirectories)
    {
        $scannerFactory = new ScannerFactory();
        $scanner = $scannerFactory->newInstance($this->visitor);
        $scanner->traverse($this->outPath);

        self::assertCount($this->leafCounter, $expectedFiles);
        self::assertCount($this->nodeCounter, $expectedDirectories);

        self::assertEquals($expectedFiles, $this->leafLog);
        self::assertEquals($expectedDirectories, $this->nodeLog);
    }


    /**
     * @return array[]
     */
    public function getCase()
    {
        return [
            [//line1
                [//files
                    'file1.txt',
                    'file7.txt',
                    'file6.txt',
                    'file7.txt',
                    'file5.txt',
                    'file4.txt',
                    'file3.txt',
                    'file2.txt',
                ],
                [//directories
                    'childL',
                    'root',
                    'child1',
                    'child2',
                    'child3',
                    'child4',
                    'child5',
                ]
            ],
        ];
    }

    /**
     * @dataProvider getCase2
     */
    public function testSearchWithFileSecondLevelFilter($expectedFiles, $expectedDirectories)
    {
        $scannerFactory = new ScannerFactory();
        $scannerFactory->setFileSecondLevelFilter(new class() implements SecondLevelFilter {
            public function execute(string $parentElement, string $currentElement): ?string
            {
                return 'file7.txt' === $currentElement ? null : $currentElement;
            }
        });
        $scanner = $scannerFactory->newInstance($this->visitor);

        $scanner->traverse($this->outPath);

        self::assertCount($this->leafCounter, $expectedFiles);
        self::assertCount($this->nodeCounter, $expectedDirectories);

        self::assertEquals($expectedFiles, $this->leafLog);
        self::assertEquals($expectedDirectories, $this->nodeLog);
    }

    /**
     * @return array[]
     */
    public function getCase2()
    {

        return [
            [//line1
                [//files
                    'file1.txt',
                    'file6.txt',
                    'file5.txt',
                    'file4.txt',
                    'file3.txt',
                    'file2.txt',
                ],
                [//directories
                    'childL',
                    'root',
                    'child1',
                    'child2',
                    'child3',
                    'child4',
                    'child5',
                ]
            ],
        ];
    }

    /**
     * @dataProvider getCase3
     */
    public function testSearchWithDirectorySecondLevelFilter($expectedFiles, $expectedDirectories)
    {
        $scannerFactory = new ScannerFactory();
        $scannerFactory->setDirectorySecondLevelFilter(new class() implements SecondLevelFilter {
            public function execute(string $parentElement, string $currentElement): ?string
            {
                return 'child1' === $currentElement ? null : $currentElement;
            }
        });
        $scanner = $scannerFactory->newInstance($this->visitor);

        $scanner->traverse($this->outPath);

        self::assertCount($this->leafCounter, $expectedFiles);
        self::assertCount($this->nodeCounter, $expectedDirectories);

        self::assertEquals($expectedFiles, $this->leafLog);
        self::assertEquals($expectedDirectories, $this->nodeLog);
    }

    /**
     * @return array[]
     */
    public function getCase3()
    {

        return [
            [//line1
                [//files
                    'file1.txt',
                    'file7.txt',
                    'file6.txt',
                    'file7.txt',
                    'file5.txt',
                    'file4.txt',
                    'file3.txt',
                    'file2.txt',
                ],
                [//directories
                    'childL',
                    'root',
                    'child2',
                    'child3',
                    'child4',
                    'child5',
                ]
            ],
        ];
    }

    /**
     * @dataProvider getCase4
     */
    public function testSearchWithFileMultiTarget($expectedFiles, $expectedDirectories)
    {
        $scannerFactory = new ScannerFactory();

        $scannerFactory->setFileSecondLevelFilter(new class() implements SecondLevelFilter {
            public function execute(string $parentElement, string $currentElement): ?string
            {
                return 'file7.txt' === $currentElement ? $currentElement : null;
            }
        });
        $scannerFactory->setFileMultiTarget(true);
        $scanner = $scannerFactory->newInstance($this->visitor);

        $scanner->traverse($this->outPath);

        self::assertCount($this->leafCounter, $expectedFiles);
        self::assertCount($this->nodeCounter, $expectedDirectories);

        self::assertEquals($expectedFiles, $this->leafLog);
        self::assertEquals($expectedDirectories, $this->nodeLog);
    }

    /**
     * @return array[]
     */
    public function getCase4()
    {
        return [
            [//line1
                [//files
                    'file7.txt',
                    'file7.txt',
                ],
                [//directories
                    'childL',
                    'root',
                    'child1',
                    'child2',
                    'child3',
                    'child4',
                    'child5',
                ]
            ],
        ];
    }

    /**
     * @dataProvider getCase5
     */
    public function testSearchWithFileMultiTargetFalse($expectedFiles, $expectedDirectories)
    {
        $scannerFactory = new ScannerFactory();

        $scannerFactory->setFileSecondLevelFilter(new class() implements SecondLevelFilter {
            public function execute(string $parentElement, string $currentElement): ?string
            {
                return 'file7.txt' === $currentElement ? $currentElement : null;
            }
        });
        $scannerFactory->setFileMultiTarget(false);
        $scanner = $scannerFactory->newInstance($this->visitor);

        $scanner->traverse($this->outPath);

        self::assertCount($this->leafCounter, $expectedFiles);
        self::assertCount($this->nodeCounter, $expectedDirectories);

        self::assertEquals($expectedFiles, $this->leafLog);
        self::assertEquals($expectedDirectories, $this->nodeLog);
    }

    /**
     * @return array[]
     */
    public function getCase5()
    {
        return [
            [//line1
                [//files
                    'file7.txt',
                ],
                [//directories
                    'childL',
                    'root',
                    'child1', //why three? because the file "file7.txt" and the directory "child1" are in the same directory and sorted, so child1 was the first and the statistics were included.
                ]
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function scanStarted(AbstractTraversalStrategy $scanStrategy, $detect): void
    {

    }

    /**
     * @inheritDoc
     */
    public function scanCompleted(AbstractTraversalStrategy $scanStrategy, $detect): void
    {

    }

    /**
     * @inheritDoc
     */
    public function visitLeaf(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentElement, mixed $data = null): void
    {
        $this->leafCounter++;
        $this->leafLog [] = $currentElement;
    }

    /**
     * @inheritDoc
     */
    public function visitNode(AbstractTraversalStrategy $scanStrategy, mixed $parentNode, mixed $currentNode, mixed $data = null): void
    {
        $this->nodeCounter++;
        $this->nodeLog[] = $currentNode;
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        $this->leafCounter = 0;
        $this->nodeCounter = 0;
        $this->nodeLog = [];
        $this->leafLog = [];
        parent::tearDown();
    }

}