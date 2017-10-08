<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Crypto\Key;

use Mdanter\Ecc\Crypto\Key\PublicKey;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Primitives\CurveFp;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Primitives\Point;
use Mdanter\Ecc\Tests\AbstractTestCase;

class PublicKeyTest extends AbstractTestCase
{

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Generator point has x and y out of range
     */
    public function testBadPointForGenerator()
    {
        $adapter = EccFactory::getAdapter();
        $generator192 = EccFactory::getNistCurves($adapter)->generator192();
        $generator384 = EccFactory::getNistCurves($adapter)->generator384();

        $tooLarge = $generator384->createPrivateKey()->getPublicKey()->getPoint();
        new PublicKey($adapter, $generator192, $tooLarge);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Curve for given point not in common with GeneratorPoint
     */
    public function testPointGeneratorMismatch()
    {
        $adapter = EccFactory::getAdapter();
        $generator384 = EccFactory::getNistCurves($adapter)->generator384();

        $generator192 = EccFactory::getNistCurves($adapter)->generator192();
        $mismatchPoint = $generator192->createPrivateKey()->getPublicKey()->getPoint();

        new PublicKey($adapter, $generator384, $mismatchPoint);
    }

    public function testInstance()
    {
        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getNistCurves($adapter)->generator192();
        $curve = $generator->getCurve();
        $point = $generator->createPrivateKey()->getPublicKey()->getPoint();
        $key = new PublicKey($adapter, $generator, $point);

        $this->assertInstanceOf(CurveFp::class, $key->getCurve());
        $this->assertSame($curve, $key->getCurve());
        $this->assertInstanceOf(GeneratorPoint::class, $key->getGenerator());
        $this->assertSame($generator, $key->getGenerator());
        $this->assertInstanceOf(Point::class, $key->getPoint());
        $this->assertSame($point, $key->getPoint());
    }
}
