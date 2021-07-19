<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Math;

use GMP;
use Mdanter\Ecc\Exception\NumberTheoryException;
use Mdanter\Ecc\Util\BinaryString;

/**
 * Class ConstantTimeMath
 *
 * This class extends GmpMath to replace some GMP functions with algorithms
 * guaranteed to be constant-time.
 *
 * @package Mdanter\Ecc\Math
 */
class ConstantTimeMath extends GmpMath
{
    /**
     * Compare two GMP objects, without timing leaks.
     *
     * @param GMP $first
     * @param GMP $other
     * @return int -1 if $first < $other
     *              0 if $first === $other
     *              1 if $first > $other
     */
    public function cmp(GMP $first, GMP $other): int
    {
        /**
         * @var GMP $left
         * @var GMP $right
         * @var int $length
         */
        list($left, $right, $length) = $this->normalizeLengths($first, $other);
        $gt = 0;
        $eq = 1;
        $i = $length;
        while ($i !== 0) {
            --$i;
            $gt |= (($this->ord($right[$i]) - $this->ord($left[$i])) >> 8) & $eq;
            $eq &= (($this->ord($right[$i]) ^ $this->ord($left[$i])) -1) >> 8;
        }
        return ($gt + $gt + $eq) - 1;
    }

    /**
     * {@inheritDoc}
     * @see GmpMathInterface::inverseMod()
     */
    public function inverseMod(GMP $a, GMP $m): GMP
    {
        list($x, $y) = $this->binaryGcd($a, $m);
        if (!$this->equals($y, \gmp_init(1))) {
            throw new NumberTheoryException('No inverse exists for these two numbers');
        }

        return $x;
    }

    /**
     * Stein's Algorithm (Binary GCD)
     *
     * Based on algorithm 14.61 from the Handbook of Applied Cryptography
     *
     * @param GMP $X
     * @param GMP $Y
     * @return GMP[] ($gcd, $inverse)
     */
    public function binaryGcd(GMP $X, GMP $Y): array
    {
        // Don't mutate the input parameters
        $x = clone $X;
        $y = clone $Y;
        $g = \min($this->trailingZeroes($x), $this->trailingZeroes($y));
        $x = $this->rightShift($x, $g);
        $x = $this->rightShift($x, $g);
        $u = clone $x;
        $v = clone $y;

        $zero = \gmp_init(0, 10);
        $a = \gmp_init(1, 10);
        $b = \gmp_init(0, 10);
        $c = \gmp_init(0, 10);
        $d = \gmp_init(1, 10);

        do {
            for ($bits = $this->trailingZeroes($u); $bits > 0; --$bits) {
                $u = $this->rightShift($u, 1);
                $swap = (~$this->lsb($a) & ~$this->lsb($b)) & 1;

                $a = $this->select($swap, $a, $this->add($a, $y));
                $a = $this->rightShift($a, 1);

                $b = $this->select($swap, $b, $this->sub($b, $x));
                $b = $this->rightShift($b, 1);
            }

            for ($bits = $this->trailingZeroes($v); $bits > 0; --$bits) {
                $v = $this->rightShift($v, 1);
                $swap = (~$this->lsb($c) & ~$this->lsb($d)) & 1;

                $c = $this->select($swap, $c, $this->add($c, $y));
                $c = $this->rightShift($c, 1);

                $d = $this->select($swap, $d, $this->sub($d, $x));
                $d = $this->rightShift($d, 1);
            }

            $cmp = $this->cmp($u, $v);
            /*
             | cmp(u, v) | swap |
             +---------------+------+
             | -1            |    0 |
             |  0            |    1 |
             |  1            |    1 |
             */
            // if ($u >= $v):
            $swap = 1 - (($cmp >> 1) & 1);

            // swap = (1 - (compare_alt(u, v)[0] >>> 31));
            $u = $this->select($swap, $this->sub($u, $v), $u);
            $a = $this->select($swap, $this->sub($a, $c), $a);
            $b = $this->select($swap, $this->sub($b, $d), $b);

            $swap = 1 - $swap;
            // else:
            $v = $this->select($swap, $this->sub($v, $u), $v);
            $c = $this->select($swap, $this->sub($c, $a), $c);
            $d = $this->select($swap, $this->sub($d, $b), $d);
        } while (!$this->equals($u, $zero));

        return [$c, $this->leftShift($v, $g)];
    }

    /**
     * Constant-time conditional select.
     *
     * returns ($bit === 1 ? $a : $b)
     *
     * @param int $bit
     * @param GMP $a
     * @param GMP $b
     * @return GMP
     */
    public function select(int $bit, GMP $a, GMP $b): GMP
    {
        // Handle the sign bits (for multiplying later)
        $a_sign = gmp_sign($a);
        $b_sign = gmp_sign($b);
        /* if bit: sign = a_sign
         * else: sign = b_sign
         */
        $sign = $b_sign ^ (($a_sign ^ $b_sign) & -$bit);

        // ($mask = $bit ? 0xff : 0x00) without branches
        $mask = -($bit & 1) & 0xff;

        // Work with the positive hex values:
        /**
         * @var GMP $left
         * @var GMP $right
         * @var int $length
         */
        list($left, $right, $length) = $this->normalizeLengths($a, $b);

        $out = [];
        for ($i = 0; $i < $length; ++$i) {
            $l = $this->ord($left[$i]);
            $r = $this->ord($right[$i]);
            $out[$i] = $this->chr($r ^ (($l ^ $r) & $mask));
        }
        // Re-multiply the sign bit:
        return $this->mul(
            gmp_init(bin2hex(implode('', $out)), 16),
            gmp_init($sign, 10)
        );
    }

    /**
     * How many trailing zero bits are in this number?
     *
     * @param GMP $num
     * @return int
     */
    public function trailingZeroes(GMP $num): int
    {
        return \max(
            0,
            \gmp_scan1($num, 0)
        );
    }

    /**
     * Get the least significant bit of $num.
     *
     * @param GMP $num
     * @return int
     */
    public function lsb(GMP $num): int
    {
        return gmp_intval($num) & 1;
    }

    /**
     * Get an unsigned integer for the character in the provided string at index 0.
     *
     * @param string $chr
     * @return int
     */
    public function ord(string $chr): int
    {
        return unpack('C', $chr)[1];
    }

    /**
     * Turn an integer in the range [0, 255] into a string character.
     * Unlike PHP's chr(), this doesn't have a cache-timing leak.
     *
     * @param int $c
     * @return string
     */
    public function chr(int $c): string
    {
        return pack('C', $c);
    }

    /**
     * Normalize the lengths of two input numbers.
     *
     * @param GMP $a
     * @param GMP $b
     * @return array<array-key, GMP|int>
     */
    public function normalizeLengths(GMP $a, GMP $b): array
    {
        $a_hex = gmp_strval(gmp_abs($a), 16);
        $b_hex = gmp_strval(gmp_abs($b), 16);
        $length = max(BinaryString::length($a_hex), BinaryString::length($b_hex));
        $length += $length & 1;

        $left = hex2bin(str_pad($a_hex, $length, '0', STR_PAD_LEFT));
        $right = hex2bin(str_pad($b_hex, $length, '0', STR_PAD_LEFT));
        $length >>= 1;
        return [$left, $right, $length];
    }
}
