<?php

namespace Mdanter\Ecc\Tests\Random;


use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Random\URandomNumberGenerator;

class URandomNumberGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneratesData()
    {
        $bits = 16;
        $bits16 = pow(2, $bits)-1;
        $math = new Gmp();
        $rng = new URandomNumberGenerator($math);
        $hex = $math->decHex($rng->generate($bits16));
        $this->assertEquals(4, strlen($hex));
    }
}
