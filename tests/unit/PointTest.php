<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Point;
use Mdanter\Ecc\UnsafePoint;
use Mdanter\Ecc\CurveFp;

class PointTest extends \PHPUnit_Framework_TestCase
{
    public function testAddInfinityReturnsOriginalPoint()
    {
        $adapter = new Gmp();
        $curve = new CurveFp(23, 1, 1, $adapter);
        $infinity = $curve->getInfinity();

        $point = new Point($adapter, $curve, 13, 7, 7);

        $sum = $point->add($infinity);
        $this->assertSame($point, $sum);

        $sum = $infinity->add($point);
        $this->assertSame($point, $sum);

    }
}
