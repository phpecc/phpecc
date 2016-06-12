<?php

namespace Mdanter\Ecc\Tests\Curves;


use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Tests\AbstractTestCase;

class NamedCurveFpTest extends AbstractTestCase
{
    public function testInstance()
    {
        $curve = EccFactory::getNistCurves()->curve384();
        $this->assertInstanceOf(NamedCurveFp::class, $curve);;
        $this->assertEquals('nist-p384', $curve->getName());
    }
}