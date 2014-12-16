<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\MathAdapter;

class MathTest extends AbstractTestCase
{
    private $knownPrimes;

    private $startPrime = 31;

    private $primeCount = 10;

    protected function setUp()
    {
        $file = TEST_DATA_DIR.'/primes.lst';

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

    private $hexDecMap = [
    	'00' => 0,
		'01' => 1,
		'02' => 2,
		'03' => 3,
		'04' => 4,
		'05' => 5,
		'06' => 6,
		'07' => 7,
		'08' => 8,
		'09' => 9,
		'0a' => 10,
		'0b' => 11,
		'0c' => 12,
		'0d' => 13,
		'0e' => 14,
		'0f' => 15
    ];

    /**
     * @dataProvider getAdapters
     */
    public function testDecHex(MathAdapter $adapter)
    {
    	foreach ($this->hexDecMap as $hex => $dec) {
    		$actual = $adapter->decHex($dec);
    		$this->assertTrue($hex === $actual, "$hex === $actual");
    	}
    }

    /**
     * @dataProvider getAdapters
     */
    public function testHexDec(MathAdapter $adapter)
    {
    	foreach ($this->hexDecMap as $hex => $dec) {
    		$actual = $adapter->hexDec($hex);
    		$this->assertTrue($dec === $actual, "$dec === $actual");
    	}
    }

    /**
     * @dataProvider getAdapters
     */
    public function testStrictIntegerReturnValues(MathAdapter $math)
    {
        $x = 10;
        $y = 4;

        $mod = $math->mod($x, $y);
        $this->assertTrue(is_string($mod) && ! is_resource($mod));

        $add = $math->add($x, $y);
        $this->assertTrue(is_string($add) && ! is_resource($add));

        $sub = $math->sub($add, $y);
        $this->assertTrue(is_string($sub) && ! is_resource($sub));

        $mul = $math->mul($x, $y);
        $this->assertTrue(is_string($mul) && ! is_resource($mul));

        $div = $math->div($mul, $y);
        $this->assertTrue(is_string($div) && ! is_resource($div));

        $pow = $math->pow($x, $y);
        $this->assertTrue(is_string($pow) && ! is_resource($div));

        $rand = $math->rand($x);
        $this->assertTrue(is_string($rand) && ! is_resource($rand));

        $powmod = $math->powmod($x, $y, $y);
        $this->assertTrue(is_string($powmod) && ! is_resource($powmod));

        $bitwiseand = $math->bitwiseAnd($x, $y);
        $this->assertTrue(is_string($bitwiseand) && ! is_resource($bitwiseand));

        $hexdec = $math->decHex($x);
        $this->assertTrue(is_string($hexdec) && ! is_resource($hexdec));

        $dechex = $math->hexDec($hexdec);
        $this->assertTrue(is_string($dechex) && ! is_resource($dechex));
    }
    /**
     * @dataProvider getAdapters
     */
    public function testKnownPrimesAreCorrectlyDetected(MathAdapter $math)
    {
        foreach ($this->knownPrimes as $key => $prime) {
            if (trim($prime) == '') {
                user_error('Empty prime number detected from line #'.($key + 1), E_USER_WARNING);
            }

            $this->assertTrue($math->isPrime($prime), 'Prime "'.$prime.'" is not detected as prime.');
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
