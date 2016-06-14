<?php

namespace Mdanter\Ecc\Tests\Primitives;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Math\ModularArithmetic;
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
}
