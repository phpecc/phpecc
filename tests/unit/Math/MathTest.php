<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Math;

use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Tests\AbstractTestCase;

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

    private $decHexMap = array(
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
        '0f' => 15,
    );

    /**
     * @dataProvider getAdapters
     * @param GmpMathInterface $adapter
     */
    public function testDecHex(GmpMathInterface $adapter)
    {
        foreach ($this->decHexMap as $hex => $dec) {
            $actual = $adapter->decHex((string) $dec);
            $this->assertTrue($hex === $actual, "$hex === $actual");
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unable to convert negative integer to string
     */
    public function testDecHexFailure()
    {
        $math = new GmpMath();
        $math->decHex('-1');
    }

    /**
     * @dataProvider getAdapters
     * @param GmpMathInterface $adapter
     */
    public function testHexDec(GmpMathInterface $adapter)
    {
        foreach ($this->decHexMap as $hex => $dec) {
            $actual = $adapter->hexDec($hex);
            $this->assertEquals($actual, $dec);
        }
    }

    /**
     * @dataProvider getAdapters
     * @param GmpMathInterface $math
     */
    public function testStrictIntegerReturnValues(GmpMathInterface $math)
    {
        $x = gmp_init(10, 10);
        $y = gmp_init(4, 10);

        $mod = $math->mod($x, $y);
        $this->assertTrue(is_object($mod) && $mod instanceof \GMP);

        $add = $math->add($x, $y);
        $this->assertTrue(is_object($add) && $add instanceof \GMP);

        $sub = $math->sub($add, $y);
        $this->assertTrue(is_object($sub) && $sub instanceof \GMP);

        $mul = $math->mul($x, $y);
        $this->assertTrue(is_object($mul) && $mul instanceof \GMP);

        $div = $math->div($mul, $y);
        $this->assertTrue(is_object($div) && $div instanceof \GMP);

        $pow = $math->pow($x, 4);
        $this->assertTrue(is_object($pow) && $pow instanceof \GMP);

        $powmod = $math->powmod($x, $y, $y);
        $this->assertTrue(is_object($powmod) && $powmod instanceof \GMP);

        $bitwiseand = $math->bitwiseAnd($x, $y);
        $this->assertTrue(is_object($bitwiseand) && $bitwiseand instanceof \GMP);

        $hexdec = $math->decHex('10');
        $this->assertTrue(is_string($hexdec));

        $dechex = $math->hexDec($hexdec);
        $this->assertTrue(is_string($dechex));
    }

    /**
     * @dataProvider getAdapters
     * @param GmpMathInterface $math
     */
    public function testKnownPrimesAreCorrectlyDetected(GmpMathInterface $math)
    {
        foreach ($this->knownPrimes as $key => $prime) {
            if ($prime === null || $prime === '') {
                user_error('Empty prime number detected from line #'.($key + 1), E_USER_WARNING);
            }

            $prime = gmp_init($prime, 10);
            $this->assertTrue($math->isPrime($prime), 'Prime "'.$math->toString($prime).'" is not detected as prime.');
        }
    }

    /**
     * @dataProvider getAdapters
     * @param GmpMathInterface $math
     */
    public function testGetNextPrimes(GmpMathInterface $math)
    {

        $currentPrime = $math->nextPrime(gmp_init($this->startPrime));

        for ($i = 0; $i < $this->primeCount; $i ++) {
            $currentPrime = $math->nextPrime($currentPrime);
            $this->assertTrue($math->isPrime($currentPrime));

            $this->assertContains(gmp_strval($currentPrime, 10), $this->knownPrimes);
        }
    }

    /**
     * @dataProvider getAdapters
     * @param GmpMathInterface $math
     */
    public function testMultInverseModP(GmpMathInterface $math)
    {
        $one = gmp_init(1, 10);
        for ($i = 0; $i < 100; $i ++) {
            $m = gmp_init(rand(20, 10000), 10);

            for ($j = 0; $j < 100; $j ++) {
                $a = gmp_init(rand(1, (int) gmp_strval(gmp_sub($m, 1), 10)), 10);

                if ($math->cmp($math->gcd2($a, $m), $one) == 0) {
                    $inv = $math->inverseMod($a, $m);
                    $check = $math->cmp($inv, gmp_init(0)) <= 0
                        && $math->cmp($inv, $m) >= 0
                        && $math->cmp($math->mod($math->mul($a, $inv), $m), $one) !== 0;
                    $this->assertFalse($check);
                }
            }
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unable to convert negative integer to string
     */
    public function testIntToStringFailure()
    {
        $math = new GmpMath();
        $math->intToString(gmp_init(-1, 10));
    }

    public function testIntToStringEmpty()
    {
        $math = new GmpMath();
        $string = $math->intToString(gmp_init(0, 10));
        $this->assertEquals(chr(0), $string);
    }

    public function testIsPrimeNot()
    {
        $math = new GmpMath();
        $this->assertFalse($math->isPrime(gmp_init(4, 10)));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Negative exponents (-1) not allowed
     */
    public function testPowModNegativeExponent()
    {
        $math = new GmpMath();
        $math->powmod(gmp_init(4, 10), gmp_init(-1), gmp_init(10));
    }

    public function getIntegers()
    {
        return $this->_getAdapters([
            [ "93259851702730122119414267054829365377133477668952701236709439268796255744233460191271607985102340890642418263347620816406406530568060493365943850327635665063346240624828866919272799277236806918436038761806088791569287622430216141779792651885991485925108198932978264734140243372160469019702162736067941749080", "1233250843753755804484142135313865659065145699339" ],
            [ "104443069266675154894494056547352496058317849841446240909381080038746954346358262165447797645375413679557833682500843057787107515306791353774008332913645491778683429942183034018591085242029712668821822870400768300036723574280615208480888561125874783371735647662718238661201806144617184418368531833097847399412", "682684192617274218271289237226465578682254228741"],
            [ "172841652009826205348651485079931564044154859094887053263544940997757724313743387438996921752290855965047976308856703202195438505015061627861419240617458979464242008737456945030261548251059024595728958588559943472687221820376770391244791727824371868459518434718330452193247973239891421410952402113233100227293", "230482896290780683372840719752000455007705001565" ]
        ]);
    }

    /**
     * @dataProvider getIntegers
     */
    public function testDigestInteger(GmpMathInterface $math, $integer, $result)
    {
        $integer = gmp_init($integer, 10);
        $this->assertEquals($result, gmp_strval($math->digestInteger($integer), 10));
    }

    public function fixedSizeIntProvider()
    {
        $two = gmp_init(2, 10);
        $maxRange8 = gmp_sub(gmp_pow($two, 8), gmp_init(1));
        $maxRange16 = gmp_sub(gmp_pow($two, 16), gmp_init(1));
        $maxRange24 = gmp_sub(gmp_pow($two, 24), gmp_init(1));
        $maxRange32 = gmp_sub(gmp_pow($two, 32), gmp_init(1));
        $bits519Unpadded = gmp_init("7d48a8cdd8ea2de81d9ad3d5b6597472264bd2c1da2e6ae175dd50397d7812383dce09988bfce60b370e5a0f3eb0090d18545935b77ba521f5db598bf3ed03616a", 16);
        $bits519Padded = gmp_init("007d48a8cdd8ea2de81d9ad3d5b6597472264bd2c1da2e6ae175dd50397d7812383dce09988bfce60b370e5a0f3eb0090d18545935b77ba521f5db598bf3ed03616a", 16);
        return $this->_getAdapters([
            [gmp_init(0), 1, "00"],
            [gmp_init(1), 1, "01"],
            [$maxRange8, 1, "ff"],

            [gmp_init(0), 2, "0000"],
            [$maxRange8, 2, "00ff"],
            [gmp_init(256), 2, "0100"],
            [$maxRange16, 2, "ffff"],

            [gmp_init(0), 4, "00000000"],
            [gmp_init(1), 4, "00000001"],
            [gmp_init(2), 4, "00000002"],
            [$maxRange8, 4, "000000ff"],
            [$maxRange16, 4, "0000ffff"],
            [$maxRange24, 4, "00ffffff"],
            [$maxRange32, 4, "ffffffff"],

            [$bits519Unpadded, 66, "007d48a8cdd8ea2de81d9ad3d5b6597472264bd2c1da2e6ae175dd50397d7812383dce09988bfce60b370e5a0f3eb0090d18545935b77ba521f5db598bf3ed03616a"],
            [$bits519Padded, 66, "007d48a8cdd8ea2de81d9ad3d5b6597472264bd2c1da2e6ae175dd50397d7812383dce09988bfce60b370e5a0f3eb0090d18545935b77ba521f5db598bf3ed03616a"],
        ]);
    }

    /**
     * @param GmpMathInterface $math
     * @param \GMP $integer
     * @param int $size
     * @param string $expectHex
     * @dataProvider fixedSizeIntProvider
     */
    public function testFixedSizeInt(GmpMathInterface $math, \GMP $integer, $size, $expectHex)
    {
        // convert to $binary
        $binary = $math->intToFixedSizeString($integer, $size);

        // check binary against expectHex
        $hex = unpack("H*", $binary)[1];
        $this->assertEquals($expectHex, $hex, 'binary should match expected value');

        // convert to int
        $read = $math->stringToInt($binary);
        $this->assertEquals(0, gmp_cmp($read, $integer), 'numbers should equal after reading');
    }

    public function testNegativeSizeDisallowed()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Byte size cannot be negative");

        (new GmpMath())->intToFixedSizeString(gmp_init(0), -1);
    }

    public function testNegativeValueDisallowed()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("x was negative - not yet supported");

        (new GmpMath())->intToFixedSizeString(gmp_init(-1), 1);
    }

    /**
     * @return array
     */
    public function tooLargeProvider()
    {
        return $this->_getAdapters([
            [gmp_init(1), 0],
            [gmp_init(256), 1],
            [gmp_init(65536), 2],
            [gmp_pow(2, 32), 1],
            [gmp_pow(2, 32), 2],
            [gmp_pow(2, 32), 3],
            [gmp_pow(2, 32), 4],
        ]);
    }

    /**
     * @param GmpMathInterface $math
     * @param \GMP $integer
     * @param int $size
     * @dataProvider tooLargeProvider
     */
    public function testLargeValuesRejected(GmpMathInterface $math, \GMP $integer, $size)
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Number overflows byte size");

        $math->intToFixedSizeString($integer, $size);
    }
}
