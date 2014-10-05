<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Points;
use Mdanter\Ecc\Point;

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
        $sum = $point->add(Points::infinity());

        $this->assertSame($point, $sum);
    }
}
