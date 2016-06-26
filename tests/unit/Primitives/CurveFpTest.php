<?php

namespace Mdanter\Ecc\Tests\Primitives;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Math\ModularArithmetic;
use Mdanter\Ecc\Primitives\CurveFp;
use Mdanter\Ecc\Primitives\CurveParameters;
use Mdanter\Ecc\Tests\AbstractTestCase;

class CurveFpTest extends AbstractTestCase
{
    public function testInstance()
    {
        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getNistCurves($adapter)->generator521();
        $curve = $generator->getCurve();

        // Test ModularArithmetic is returned, and initialized
        // with correct prime by testing 0 = (p + 0) % p
        $modAdapter = $curve->getModAdapter();
        $zero = gmp_init(0);
        $this->assertInstanceOf(ModularArithmetic::class, $modAdapter);
        $this->assertTrue($adapter->equals($zero, $modAdapter->add($curve->getPrime(), $zero)));

        $this->assertTrue($curve->contains($generator->getX(), $generator->getY()));

        // Test infinity point is returned
        $infinityPoint = $curve->getInfinity();
        $this->assertTrue($infinityPoint->isInfinity());

        // Check equality tests
        $differentCurve = EccFactory::getNistCurves()->curve192();
        $this->assertEquals(1, $curve->cmp($differentCurve));
        $this->assertEquals(0, $curve->cmp($curve));
        $this->assertFalse($curve->equals($differentCurve));
        $this->assertTrue($curve->equals($curve));

    }


    public function testDebugInfo()
    {
        $adapter = new GmpMath();
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $curve = new CurveFp($parameters, $adapter);

        $debug = $curve->__debugInfo();
        $this->assertTrue(isset($debug['a']));
        $this->assertTrue(isset($debug['b']));
        $this->assertTrue(isset($debug['prime']));
    }
}
