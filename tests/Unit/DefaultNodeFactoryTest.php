<?php

namespace Test\Unit;

use Vinograd\FileSearch\DefaultNodeFactory;
use PHPUnit\Framework\TestCase;
use Vinograd\Scanner\Leaf;
use Vinograd\Scanner\Node;

class DefaultNodeFactoryTest extends TestCase
{

    public function testCreateNode()
    {
        $factory = new DefaultNodeFactory();
        $directory = $factory->createNode('/var/www', 'folder');
        self::assertInstanceOf(Node::class, $directory);
        self::assertEquals('/var/www/folder', $directory->getSource());
    }

    public function testCreateLeaf()
    {
        $factory = new DefaultNodeFactory();
        $file = $factory->createLeaf('/var/www', 'file.txt');
        self::assertInstanceOf(Leaf::class, $file);
        self::assertEquals('/var/www/file.txt', $file->getSource());
    }
}
