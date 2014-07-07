<?php

namespace Mdanter\Ecc\Tests\Gmp;

use Mdanter\Ecc\NumberTheory;

/* @codeCoverageIgnoreStart */
if (! extension_loaded('gmp')) {
    return;
}

if (! defined('USE_EXT')) {
    define('USE_EXT', 'GMP');
}
/* @codeCoverageIgnoreEnd */
class PrimeTest extends \PHPUnit_Framework_TestCase
{

    private $startPrime = 31;

    private $primeCount = 10;

    public function testGetNextPrimes()
    {
        $nextPrime = NumberTheory::next_prime($this->startPrime);
        $currentPrime = $nextPrime;
        
        for ($i = 0; $i < $this->primeCount; $i ++) {
            $currentPrime = NumberTheory::next_prime($currentPrime);
            $this->assertTrue(NumberTheory::is_prime($currentPrime));
        }
    }
}
