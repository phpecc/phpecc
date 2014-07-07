<?php

namespace PhpEcc\Tests;

/* @codeCoverageIgnoreStart */
if (! extension_loaded('gmp')) {
    return;
}
/* @codeCoverageIgnoreEnd */
class GmpPrimeTest extends \PHPUnit_Framework_TestCase
{

    private $startPrime = 31;

    private $primeCount = 10;

    public function testGetNextPrimes()
    {
        $next_prime = NumberTheory::next_prime($prime);
        $error_tally = 0;
        $cur_prime = $next_prime;

        for ($i = 0; $i < $num_primes; $i ++) {
            $cur_prime = NumberTheory::next_prime($cur_prime);
            $this->assertTrue(NumberTheory::is_prime($cur_prime));
        }
    }
}
