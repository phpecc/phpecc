<?php

namespace Mdanter\Ecc\Tests\Primitives;

use Mdanter\Ecc\Primitives\CurveParameters;
use Mdanter\Ecc\Tests\AbstractTestCase;

class CurveParametersTest extends AbstractTestCase
{
    public function testInstance()
    {
        $size = 224;
        $p = gmp_init('26959946667150639794667015087019630673557916260026308143510066298881', 10);
        $a = gmp_init(-3, 10);
        $b = gmp_init('b4050a850c04b3abf54132565044b0b7d7bfd8ba270b39432355ffb4', 16);

        $parameters = new CurveParameters($size, $p, $a, $b);

        $this->assertInternalType('int', $size);
        $this->assertEquals($size, $parameters->getSize());
        $this->assertInstanceOf(\GMP::class, $parameters->getA());
        $this->assertInstanceOf(\GMP::class, $parameters->getB());
        $this->assertInstanceOf(\GMP::class, $parameters->getPrime());
    }
}
