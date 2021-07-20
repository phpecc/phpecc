<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Math;

use Mdanter\Ecc\Math\ConstantTimeMath;

class ConstantTimeMathTest extends MathTestBase
{
    public function testCompareSigns()
    {
        $math = new ConstantTimeMath();
        $required = [
            [-1, -1, 0, 1],
            [-1,  0, 0, 0],
            [-1,  1, 0, 0],
            [ 0, -1, 1, 0],
            [ 0,  0, 0, 1],
            [ 0,  1, 0, 0],
            [ 1, -1, 1, 0],
            [ 1,  0, 1, 0],
            [ 1,  1, 0, 1],
        ];
        foreach ($required as $i => $row) {
            list($first, $other, $gt, $eq) = $row;
            list($a, $b) = $math->compareSigns($first, $other);
            $this->assertSame($a, $gt, "gt is wrong on row {$i} ({$first}, {$other}, {$gt}, {$eq})");
            $this->assertSame($b, $eq, "eq is wrong on row {$i} ({$first}, {$other}, {$gt}, {$eq})");
        }
    }

    public function testCmp()
    {
        $math = new ConstantTimeMath();
        $big    = '01' . bin2hex(random_bytes(16)) . '01';
        $bigger = '7f' . bin2hex(random_bytes(16)) . '7f';
        $a = gmp_init($big, 16);
        $b = gmp_init($bigger, 16);
        $c = gmp_init('-' . $bigger, 16); // negative

        $this->assertEquals(-1, $math->cmp($a, $b), "{$a} < {$b}");
        $this->assertEquals(0, $math->cmp($a, $a), "{$a} == {$a}");
        $this->assertEquals(0, $math->cmp($b, $b), "{$b} == {$b}");
        $this->assertEquals(1, $math->cmp($b, $a), "{$b} > {$a}");

        $this->assertEquals(-1, $math->cmp($c, $b), "{$c} < {$b}");
        $this->assertEquals(0, $math->cmp($b, $b), "{$b} == {$b}");
        $this->assertEquals(0, $math->cmp($c, $c), "{$c} == {$c}");
        $this->assertEquals(1, $math->cmp($b, $c), "{$c} > {$b}");

        $d = gmp_init(0, 10);
        $e = gmp_init(1, 10);
        $this->assertEquals(-1, $math->cmp($d, $e), "{$d} < {$e}");
        $this->assertEquals(0, $math->cmp($d, $d), "{$d} == {$d}");
        $this->assertEquals(0, $math->cmp($e, $e), "{$e} == {$e}");
        $this->assertEquals(1, $math->cmp($e, $d), "{$e} > {$d}");

        $f = gmp_init('1e0ea4fd44a90d57c67fda8e7b9fb98b5dca575e777d911e6de72dfc8cd02b55', 16);
        $g = gmp_init('ffffffff00000000ffffffffffffffffbce6faada7179e84f3b9cac2fc632551', 16);
        $this->assertEquals(-1, $math->cmp($f, $g), "{$f} < {$g}");
    }

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
            [gmp_init('ff00', 16), 8],
            [gmp_init('f000', 16), 12],
            [gmp_init('e000', 16), 13],
            [gmp_init('c000', 16), 14],
            [gmp_init('8000', 16), 15],
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

        $odd = gmp_init('-1234567', 10);
        $even = gmp_init('-2345678', 10);
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

        $left = gmp_init('-1234567', 10);
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
