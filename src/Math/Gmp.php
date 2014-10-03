<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\MathAdapter;
use Mdanter\Ecc\GmpUtils;

class Gmp implements MathAdapter
{
    function cmp($first, $other)
    {
        return gmp_cmp($first, $other);
    }

    function mod($number, $modulus)
    {
        return GmpUtils::gmpMod2($number, $modulus);
    }

    function add($augend, $addend)
    {
        return gmp_add($augend, $addend);
    }

    function sub($minuend, $subtrahend)
    {
        return gmp_sub($minuend, $subtrahend);
    }

    function mul($multiplier, $multiplicand)
    {
        return gmp_mul($multiplier, $multiplicand);
    }

    function pow($base, $exponent)
    {
        return gmp_pow($base, $exponent);
    }

    function rand($n)
    {
        return GmpUtils::gmpRandom($n);
    }
}
