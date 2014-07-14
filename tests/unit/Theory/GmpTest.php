<?php

namespace Mdanter\Ecc\Tests\Theory;

use Mdanter\Ecc\NumberTheory;
use Mdanter\Ecc\Theory\Gmp;
use Mdanter\Ecc\ModuleConfig;

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

    private $theory;

    private $knownPrimes;

    private $startPrime = 31;

    private $primeCount = 10;

    protected function setUp()
    {
        ModuleConfig::useGmp();
        
        $file = TEST_DATA_DIR. '/primes.lst';

        if (! file_exists($file)) {
            $this->fail('Primes not found');
        }

        $lines = file($file);
        if (! $lines) {
            $this->fail('Empty prime file');
        }

        $this->knownPrimes = $lines;
        $this->theory = new Gmp(NumberTheory::$smallprimes);
    }

    public function testKnownPrimesAreCorrectlyDetected()
    {
        foreach ($this->knownPrimes as $key => $prime) {
            if (trim($prime) == '') {
                user_error('Empty prime number detected from line #' . $key + 1, E_USER_WARNING);
            }

            $this->assertTrue($this->theory->isPrime($prime), 'Prime "' . $prime . '" is not detected as prime.');
        }
    }

    public function testSquareRootModPrime()
    {
        $prime = $this->startPrime;
        $squares = array();

        for ($root = 0; gmp_cmp($root, gmp_add(1, gmp_div($prime, 2))) < 0; $root = gmp_add($root, 1)) {
            $sq = gmp_strval(gmp_powm($root, 2, $prime));
            $calculated = $this->theory->squareRootModPrime($sq, $prime);
            $calc_sq = gmp_strval(gmp_powm($calculated, 2, $prime));

            $this->assertFalse(gmp_cmp($calculated, $root) != 0 && gmp_cmp(gmp_sub($prime, $calculated), $root) != 0);
        }
    }

    public function testGetNextPrimes()
    {
        $nextPrime = $this->theory->nextPrime($this->startPrime);
        $currentPrime = $nextPrime;

        for ($i = 0; $i < $this->primeCount; $i ++) {
            $currentPrime = $this->theory->nextPrime($currentPrime);
            $this->assertTrue($this->theory->isPrime($currentPrime));
        }
    }

    public function testMultInverseModP($verbose = false)
    {
        for ($i = 0; $i < 100; $i ++) {
            $m = rand(20, 10000);

            for ($j = 0; $j < 100; $j ++) {
                $a = rand(1, $m - 1);

                if ($this->theory->gcd2($a, $m) == 1) {
                    $inv = $this->theory->inverseMod($a, $m);
                    $this->assertFalse($inv <= 0 || $inv >= $m || ($a * $inv) % $m != 1);
                }
            }
        }
    }
}
