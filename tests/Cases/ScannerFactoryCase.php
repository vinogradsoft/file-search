<?php
declare(strict_types=1);

namespace Test\Cases;

use PHPUnit\Framework\TestCase;

abstract class ScannerFactoryCase extends TestCase
{
    protected function assertFilter($initialCheckerObjectValue, $filter)
    {
        $checkerObjectValueReflection = new \ReflectionObject($initialCheckerObjectValue);
        $filterProperty = $checkerObjectValueReflection->getProperty('filter');
        $nextProperty = $checkerObjectValueReflection->getProperty('next');
        $filterProperty->setAccessible(true);
        $nextProperty->setAccessible(true);
        $filterObjectValue = $filterProperty->getValue($initialCheckerObjectValue);
        self::assertSame($filter, $filterObjectValue);
        return $nextProperty->getValue($initialCheckerObjectValue);
    }
}