<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Math;

use Mdanter\Ecc\Math\ConstantTimeMath;

class ConstantTimeMathTest extends MathTestBase
{
    public function testOrdChr()
    {
        $math = new ConstantTimeMath();
        $byte = random_bytes(1);
        $ord = $math->ord($byte);
        $this->assertGreaterThan(-1, $ord);
        $this->assertLessThan(256, $ord);
        $chr = $math->chr($ord);
        $this->assertSame(bin2hex($chr), bin2hex($byte));
    }

    public function testTrailingZeroes()
    {
        $math = new ConstantTimeMath();
        $vectors = [
            [gmp_init('0000', 16), 0], // gmp_scan1($x, 0) says -1, we say 0
            [gmp_init('ffff', 16), 0],
            [gmp_init('fffe', 16), 1],
            [gmp_init('fffc', 16), 2],
            [gmp_init('fff8', 16), 3],
            [gmp_init('fff0', 16), 4],
        ];
        foreach ($vectors as $vector) {
            list($in, $expect) = $vector;
            $this->assertEquals(
                $expect,
                $math->trailingZeroes($in)
            );
        }
    }

    public function testLsb()
    {
        $math = new ConstantTimeMath();
        $odd = gmp_init('1234567', 10);
        $even = gmp_init('2345678', 10);
        $this->assertEquals(1, $math->lsb($odd));
        $this->assertEquals(0, $math->lsb($even));
    }

    public function testSelect()
    {
        $math = new ConstantTimeMath();
        $left = gmp_init('1234567', 10);
        $right = gmp_init('7654321', 10);
        $this->assertEquals(
            $left,
            $math->select(1, $left, $right)
        );
        $this->assertEquals(
            $right,
            $math->select(0, $left, $right)
        );
    }
}
