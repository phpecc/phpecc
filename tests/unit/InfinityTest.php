<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\Infinity;
use Mdanter\Ecc\Points;

class InfinityTest extends \PHPUnit_Framework_TestCase
{

    public function testInfinityEqualsItself()
    {
        $infinity = Infinity::getInstance();

        $this->assertTrue($infinity->equals($infinity));
    }

    public function testAddInfinityReturnsInfinity()
    {
        $infinity = Points::infinity();

        $sum = $infinity->add($infinity);

        $this->assertSame($infinity, $sum);
    }

    public function testAddFinitePointReturnsFinitePoint()
    {
        $infinity = Points::infinity();
        $point = $this->getMock('\Mdanter\Ecc\PointInterface');

        $sum = $infinity->add($point);

        $this->assertSame($point, $sum);
    }
}
