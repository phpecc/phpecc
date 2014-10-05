<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\CurveFp;
use Mdanter\Ecc\Point;
use Mdanter\Ecc\MathAdapter;
use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Math\BcMath;

class MathTest extends AbstractTestCase
{

    private $knownPrimes;

    private $startPrime = 31;

    private $primeCount = 10;

    protected function setUp()
    {
        $file = TEST_DATA_DIR. '/primes.lst';

        if (! file_exists($file)) {
            $this->fail('Primes not found');
        }

        $lines = file($file);
        if (! $lines) {
            $this->fail('Empty prime file');
        }

        $this->knownPrimes = array_map(function ($i) {
            return intval($i);
        }, $lines);
    }

    /**
     * @dataProvider getAdapters
     */
    public function testKnownPrimesAreCorrectlyDetected(MathAdapter $math)
    {
        foreach ($this->knownPrimes as $key => $prime) {
            if (trim($prime) == '') {
                user_error('Empty prime number detected from line #' . ($key + 1), E_USER_WARNING);
            }

            $this->assertTrue($math->isPrime($prime), 'Prime "' . $prime . '" is not detected as prime.');
        }
    }

    /**
     * @dataProvider getAdapters
     */
    public function testGetNextPrimes(MathAdapter $math)
    {
        $currentPrime = $math->nextPrime($this->startPrime);

        for ($i = 0; $i < $this->primeCount; $i ++) {
            $currentPrime = $math->nextPrime($currentPrime);
            $this->assertTrue($math->isPrime($currentPrime));

            $this->assertContains($currentPrime, $this->knownPrimes);
        }
    }

    /**
     * @dataProvider getAdapters
     */
    public function testMultInverseModP(MathAdapter $math)
    {
        for ($i = 0; $i < 100; $i ++) {
            $m = rand(20, 10000);

            for ($j = 0; $j < 100; $j ++) {
                $a = rand(1, $m - 1);

                if ($math->gcd2($a, $m) == 1) {
                    $inv = $math->inverseMod($a, $m);
                    $this->assertFalse($inv <= 0 || $inv >= $m || ($a * $inv) % $m != 1);
                }
            }
        }
    }
}
