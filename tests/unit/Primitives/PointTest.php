<?php

namespace Mdanter\Ecc\Tests\Primitives;

use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Primitives\CurveParameters;
use Mdanter\Ecc\Primitives\Point;
use Mdanter\Ecc\Primitives\CurveFp;
use Mdanter\Ecc\Tests\AbstractTestCase;

class PointTest extends AbstractTestCase
{
    public function testDebugInfo()
    {
        $adapter = new GmpMath();
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $curve = new CurveFp($parameters, $adapter);

        $infinity = $curve->getInfinity();
        $debug = $infinity->__debugInfo();
        $this->assertTrue(isset($debug['x']));
        $this->assertTrue(isset($debug['y']));
        $this->assertTrue(isset($debug['z']));
        $this->assertTrue(isset($debug['curve']));
        
        $point = new Point($adapter, $curve, gmp_init(13, 10), gmp_init(7, 10), gmp_init(7, 10));
        $debug = $point->__debugInfo();
        $this->assertTrue(isset($debug['x']));
        $this->assertTrue(isset($debug['y']));
        $this->assertTrue(isset($debug['z']));
        $this->assertTrue(isset($debug['curve']));
    }
    
    public function testAddInfinityReturnsOriginalPoint()
    {
        $adapter = new GmpMath();
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $curve = new CurveFp($parameters, $adapter);

        $infinity = $curve->getInfinity();

        $point = new Point($adapter, $curve, gmp_init(13, 10), gmp_init(7, 10), gmp_init(7, 10));

        $sum = $point->add($infinity);
        $this->assertTrue($point->equals($sum));

        $sum = $infinity->add($point);
        $this->assertTrue($point->equals($sum));

    }

    public function testConditionalSwap()
    {
        $aa = gmp_init('104564512312317874865', 10);
        $ab = gmp_init('04156456456456456456', 10);

        $a = $aa;
        $b = $ab;

        $adapter = new GmpMath();
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $curve = new CurveFp($parameters, $adapter);

        $point = $curve->getPoint(gmp_init(13, 10), gmp_init(7, 10), gmp_init(7, 10));

        $point->cswapValue($a, $b, false);

        $this->assertEquals($adapter->toString($aa), $adapter->toString($a));
        $this->assertEquals($adapter->toString($ab), $adapter->toString($b));

        $point->cswapValue($a, $b, true);

        $this->assertEquals($adapter->toString($aa), $adapter->toString($b));
        $this->assertEquals($adapter->toString($ab), $adapter->toString($a));

        $point->cswapValue($a, $b, false);

        $this->assertEquals($adapter->toString($aa), $adapter->toString($b));
        $this->assertEquals($adapter->toString($ab), $adapter->toString($a));
    }
}
