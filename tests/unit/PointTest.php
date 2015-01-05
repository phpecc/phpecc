<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Points;
use Mdanter\Ecc\Point;
use Mdanter\Ecc\UnsafePoint;

class PointTest extends \PHPUnit_Framework_TestCase
{
    public function testAddInfinity()
    {
        $adapter = new Gmp();
        $curve = $this->getMock('\Mdanter\Ecc\CurveFpInterface');

        $curve->expects($this->once())
            ->method('contains')
            ->willReturn(true);

        $point = new Point($curve, 0, 0, null, $adapter);
        $infinity = new UnsafePoint($adapter, $curve, 0, 0, 0, true);
        $sum = $point->add($infinity);

        $this->assertSame($point, $sum);
    }
}
