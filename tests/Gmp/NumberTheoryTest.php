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
class NumberTheoryTest extends \PHPUnit_Framework_TestCase
{

    private $knownPrimes;

    protected function setUp()
    {
        $file = __DIR__ . '/../data/primes.lst';

        if (! file_exists($file)) {
            $this->fail('Primes not found');
        }

        $lines = file($file);
        if (! $lines) {
            $this->fail('Empty prime file');
        }

        $this->knownPrimes = $lines;
    }

    public function testKnownPrimesAreCorrectlyDetected()
    {
        foreach ($this->knownPrimes as $key => $prime) {
            if (trim($prime) == '') {
                user_error('Empty prime number detected from line #' . $key + 1, E_USER_WARNING);
            }

            $this->assertTrue(NumberTheory::is_prime($prime), 'Testing prime ' . $prime);
        }
    }
}
