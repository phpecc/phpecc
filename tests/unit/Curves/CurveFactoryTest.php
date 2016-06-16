<?php

namespace Mdanter\Ecc\Tests\Curves;

use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Curves\SecgCurve;
use Mdanter\Ecc\Tests\AbstractTestCase;

class CurveFactoryTest extends AbstractTestCase
{
    public function getCurveNames()
    {
        return [
            [NistCurve::NAME_P192],
            [NistCurve::NAME_P224],
            [NistCurve::NAME_P256],
            [NistCurve::NAME_P384],
            [NistCurve::NAME_P521],
            [SecgCurve::NAME_SECP_112R1],
            [SecgCurve::NAME_SECP_256R1],
            [SecgCurve::NAME_SECP_256K1],
            [SecgCurve::NAME_SECP_384R1],
        ];
    }

    /**
     * @param string $name
     * @dataProvider getCurveNames
     */
    public function testLoadsCurveByName($name)
    {
        $curve = CurveFactory::getCurveByName($name);
        $generator = CurveFactory::getGeneratorByName($name);
        $this->assertEquals($name, $curve->getName());
        $this->assertEquals($name, $generator->getCurve()->getName());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown curve.
     */
    public function testFailsOnUnknownCurve()
    {
        CurveFactory::getCurveByName('unknown');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown generator.
     */
    public function testFailsOnUnknownGenerator()
    {
        CurveFactory::getGeneratorByName('unknown');
    }
}
