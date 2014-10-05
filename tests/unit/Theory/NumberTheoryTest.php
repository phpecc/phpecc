<?php

namespace Mdanter\Ecc\Tests\Theory;

use Mdanter\Ecc\NumberTheory;
use Mdanter\Ecc\ModuleConfig;
use Mdanter\Ecc\CurveFp;
use Mdanter\Ecc\Point;
use Mdanter\Ecc\Theory\Agnostic;
use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Math\BcMath;
use Mdanter\Ecc\MathAdapter;
use Mdanter\Ecc\TheoryAdapter;

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

        $this->knownPrimes = array_map(function ($i) {
            return intval($i);
        }, $lines);
    }

    public function getTheories()
    {
        return [
            [ new Agnostic(NumberTheory::$smallprimes, new Gmp()), new Gmp() ],
            [ new Agnostic(NumberTheory::$smallprimes, new BcMath()), new BcMath() ]
        ];
    }

    /**
     * @dataProvider getTheories
     */
    public function testKnownPrimesAreCorrectlyDetected($theory, $math)
    {
        NumberTheory::setTheoryAdapter($theory);

        foreach ($this->knownPrimes as $key => $prime) {
            if (trim($prime) == '') {
                user_error('Empty prime number detected from line #' . ($key + 1), E_USER_WARNING);
            }

            $this->assertTrue($theory->isPrime($prime), 'Prime "' . $prime . '" is not detected as prime.');
        }
    }

    /**
     * @dataProvider getTheories
     */
    public function testSquareRootModPrime(TheoryAdapter $theory, MathAdapter $math)
    {
        NumberTheory::setTheoryAdapter($theory);

        $prime = $this->startPrime;
        $squares = array();

        for ($root = 0; $math->cmp($root, $math->add(1, $math->div($prime, 2))) < 0; $root = $math->add($root, 1)) {
            $sq = $math->powmod($root, 2, $prime);
            $calculated = $theory->squareRootModPrime($sq, $prime);
            $calc_sq = $math->powmod($calculated, 2, $prime);

            $this->assertFalse($math->cmp($calculated, $root) != 0 && $math->cmp($math->sub($prime, $calculated), $root) != 0);
        }
    }

    /**
     * @dataProvider getTheories
     */
    public function testGetNextPrimes($theory, $math)
    {
        NumberTheory::setTheoryAdapter($theory);

        $nextPrime = $theory->nextPrime($this->startPrime);
        $currentPrime = $nextPrime;

        for ($i = 0; $i < $this->primeCount; $i ++) {
            $currentPrime = $theory->nextPrime($currentPrime);
            $this->assertTrue($theory->isPrime($currentPrime));

            $this->assertContains($currentPrime, $this->knownPrimes);
        }
    }

    /**
     * @dataProvider getTheories
     */
    public function testMultInverseModP($theory, $math)
    {
        NumberTheory::setTheoryAdapter($theory);

        for ($i = 0; $i < 100; $i ++) {
            $m = rand(20, 10000);

            for ($j = 0; $j < 100; $j ++) {
                $a = rand(1, $m - 1);

                if ($theory->gcd2($a, $m) == 1) {
                    $inv = $theory->inverseMod($a, $m);
                    $this->assertFalse($inv <= 0 || $inv >= $m || ($a * $inv) % $m != 1);
                }
            }
        }
    }
}
