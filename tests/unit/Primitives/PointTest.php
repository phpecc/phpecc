<?php

namespace Mdanter\Ecc\Tests\Primitives;

use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Primitives\CurveParameters;
use Mdanter\Ecc\Primitives\Point;
use Mdanter\Ecc\Primitives\CurveFp;
use Mdanter\Ecc\Tests\AbstractTestCase;

class PointTest extends AbstractTestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Curve curve(1, 1, 23) does not contain point (2, 2)
     */
    public function testConstructorInvalidCurvePoint()
    {
        $adapter = new GmpMath();
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $curve = new CurveFp($parameters, $adapter);
        new Point($adapter, $curve, gmp_init(2), gmp_init(2), null, false);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage The Elliptic Curves do not match.
     */
    public function testPointAdditionCurveMismatch()
    {
        $adapter = new GmpMath();
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $curve = new CurveFp($parameters, $adapter);
        $point = new Point($adapter, $curve, gmp_init(13, 10), gmp_init(7, 10), gmp_init(7, 10));
        $point2 = CurveFactory::getGeneratorByName('secp256k1');

        $point->add($point2);
    }

    public function testCmp()
    {
        $adapter = new GmpMath();
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $curve = new CurveFp($parameters, $adapter);

        $infinity = $curve->getInfinity();

        $point = new Point($adapter, $curve, gmp_init(13, 10), gmp_init(7, 10), gmp_init(7, 10));
        $this->assertEquals(1, $point->cmp($infinity));
        $this->assertEquals(1, $infinity->cmp($point));
        $this->assertEquals(0, $point->cmp($point));
        $this->assertEquals(0, $infinity->cmp($infinity));
        $this->assertTrue($point->equals($point));
        $this->assertTrue($infinity->equals($infinity));

        $point2 = $point->getDouble();
        $this->assertEquals(1, $point->cmp($point2));
        $this->assertEquals(1, $point2->cmp($point));
        $this->assertFalse($point2->equals($point));
    }

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

    public function testMulAlreadyInfinity()
    {
        $adapter = new GmpMath();
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $curve = new CurveFp($parameters, $adapter);

        $this->assertTrue($curve->getInfinity()->mul(gmp_init(1))->isInfinity());
    }

    public function testDoubleAlreadyInfinity()
    {
        $adapter = new GmpMath();
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $curve = new CurveFp($parameters, $adapter);

        $this->assertTrue($curve->getInfinity()->mul(gmp_init(1))->isInfinity());
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

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Curve curve(-3, 41058363725152142129326129780047268409114441015993725554835256314039467401291, 115792089210356248762697446949407573530086143415290314195533631308867097853951) does not contain point (58449750472625921448203013684212508347339475414040948669953216871973381284903, 50970277084784601725958135138198442242271560603337990589052444638672428978267)
     */
    public function testRejectsInvalidPoints()
    {
        $x = gmp_init('58449750472625921448203013684212508347339475414040948669953216871973381284903', 10);
        $y = gmp_init('50970277084784601725958135138198442242271560603337990589052444638672428978267', 10);

        $generator = EccFactory::getNistCurves()->generator256();
        $generator->getPublicKeyFrom($x, $y);
    }
}
