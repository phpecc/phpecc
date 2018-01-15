<?php

namespace Mdanter\Ecc\Tests\Random;


use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Random\RandomBytesGenerator;

class RandomBytesGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneratesData()
    {
        $this->assertTrue(function_exists('random_bytes'));

        $bits = 16;
        $bits16 = pow(2, $bits)-1;
        $math = new Gmp();
        $rng = new RandomBytesGenerator($math);
        $hex = $math->decHex($rng->generate($bits16));
        $this->assertEquals(4, strlen($hex));
    }
}
