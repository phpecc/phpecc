<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Curves;

use Mdanter\Ecc\Curves\BrainpoolCurve;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Curves\SecgCurve;
use Mdanter\Ecc\Exception\UnsupportedCurveException;
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
            [BrainpoolCurve::NAME_P160R1],
            [BrainpoolCurve::NAME_P192R1],
            [BrainpoolCurve::NAME_P256R1],
            [BrainpoolCurve::NAME_P320R1],
            [BrainpoolCurve::NAME_P384R1],
            [BrainpoolCurve::NAME_P512R1],
            [SecgCurve::NAME_SECP_112R1],
            [SecgCurve::NAME_SECP_192K1],
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

    public function testFailsOnUnknownCurve()
    {
        $this->expectException(\Mdanter\Ecc\Exception\UnsupportedCurveException::class);
        $this->expectExceptionMessage('Unknown curve.');

        $curveName = 'unknown';
        try {
            CurveFactory::getCurveByName($curveName);
        } catch (UnsupportedCurveException $e) {
            $this->assertTrue($e->hasCurveName());
            $this->assertEquals($curveName, $e->getCurveName());
            throw $e;
        }
    }

    public function testFailsOnUnknownGenerator()
    {
        $this->expectException(\Mdanter\Ecc\Exception\UnsupportedCurveException::class);
        $this->expectExceptionMessage('Unknown generator.');

        $curveName = 'unknown';
        try {
            CurveFactory::getGeneratorByName($curveName);
        } catch (UnsupportedCurveException $e) {
            $this->assertTrue($e->hasCurveName());
            $this->assertEquals($curveName, $e->getCurveName());
            throw $e;
        }
    }
}
