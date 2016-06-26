<?php

namespace Mdanter\Ecc\Tests\Serializer\Util;


use FG\ASN1\Universal\ObjectIdentifier;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Primitives\CurveParameters;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Tests\AbstractTestCase;

class CurveOidMapperTest extends AbstractTestCase
{
    public function testGetNames()
    {
        $this->assertInternalType('array', CurveOidMapper::getNames());
    }

    public function testValidValues()
    {
        $G = EccFactory::getNistCurves()->generator521();
        $nistp521 = $G->getCurve();
        $nistp521oid = CurveOidMapper::getCurveOid($nistp521);
        $this->assertEquals(66, CurveOidMapper::getByteSize($nistp521));
        $this->assertInstanceOf(ObjectIdentifier::class, $nistp521oid);

        $curve = CurveOidMapper::getCurveFromOid($nistp521oid);
        $this->assertTrue($curve->equals($nistp521));

        $gen = CurveOidMapper::getGeneratorFromOid($nistp521oid);
        $this->assertTrue($G->equals($gen));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetBytesUnknownCurve()
    {
        $adapter = EccFactory::getAdapter();
        $curve = new NamedCurveFp('badcurve', new CurveParameters(10, gmp_init(1), gmp_init(1), gmp_init(1)), $adapter);
        CurveOidMapper::getByteSize($curve);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetCurveOid()
    {
        $adapter = EccFactory::getAdapter();
        $curve = new NamedCurveFp('badcurve', new CurveParameters(10, gmp_init(1), gmp_init(1), gmp_init(1)), $adapter);
        CurveOidMapper::getCurveOid($curve);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCurveUnknownOid()
    {
        $oid = new ObjectIdentifier('1.3');
        CurveOidMapper::getCurveFromOid($oid);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGeneratorUnknownOid()
    {
        $oid = new ObjectIdentifier('1.3');
        CurveOidMapper::getGeneratorFromOid($oid);
    }
}