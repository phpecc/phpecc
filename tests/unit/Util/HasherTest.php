<?php

namespace Mdanter\Ecc\Tests\Util;


use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Tests\AbstractTestCase;
use Mdanter\Ecc\Util\Hasher;

class HasherTest extends AbstractTestCase
{
    public function testHash()
    {
        $math = new Gmp();
        $algo = 'sha256';
        $hasher = new Hasher($math, $algo);

        $expected = hash('sha256', 'test');
        $hash = $hasher->hash('test');
        $this->assertEquals($expected, $hash);

        $expected = hash('sha256', 'test', false);
        $hash = $hasher->hash('test', false);
        $this->assertEquals($expected, $hash);

        $expected = hash('sha256', 'test', true);
        $hash = $hasher->hash('test', true);
        $this->assertEquals($expected, $hash);

        $this->assertEquals($algo, $hasher->getAlgo());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAlgo()
    {
        $math = new Gmp();
        new Hasher($math, 'sha127');
    }
}