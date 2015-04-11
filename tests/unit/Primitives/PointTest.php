<?php

namespace Mdanter\Ecc\Tests\Primitives;

use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Primitives\Point;
use Mdanter\Ecc\Primitives\CurveFp;
use Mdanter\Ecc\Tests\AbstractTestCase;

class PointTest extends AbstractTestCase
{
    public function testAddInfinityReturnsOriginalPoint()
    {
        $adapter = new Gmp();
        $curve = new CurveFp(23, 1, 1, $adapter);
        $infinity = $curve->getInfinity();

        $point = new Point($adapter, $curve, 13, 7, 7);

        $sum = $point->add($infinity);
        $this->assertTrue($point->equals($sum));

        $sum = $infinity->add($point);
        $this->assertTrue($point->equals($sum));

    }

    public function testConditionalSwap()
    {
        $a = '104564512312317874865';
        $b = '04156456456456456456';

        $curve = new CurveFp(23, 1, 1, new Gmp());
        $point = $curve->getPoint(13, 7, 7);

        $point->cswapValue($a, $b, false);

        $this->assertEquals('104564512312317874865', $a);
        $this->assertEquals('4156456456456456456', $b);

        $point->cswapValue($a, $b, true);

        $this->assertEquals('104564512312317874865', $b);
        $this->assertEquals('4156456456456456456', $a);

        $point->cswapValue($a, $b, false);

        $this->assertEquals('104564512312317874865', $b);
        $this->assertEquals('4156456456456456456', $a);
    }
}
