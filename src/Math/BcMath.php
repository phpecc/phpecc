<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\MathAdapter;
use Mdanter\Ecc\BcMathUtils;

class BcMath implements MathAdapter
{
    function cmp($first, $other)
    {
        return bccomp($first, $other);
    }

    function mod($number, $modulus)
    {
        return bcmod($number, $modulus);
    }

    function add($augend, $addend)
    {
        return bcadd($augend, $addend);
    }

    function sub($minuend, $subtrahend)
    {
        return bcsub($minuend, $subtrahend);
    }

    function mul($multiplier, $multiplicand)
    {
        return bcmul($multiplier, $multiplicand);
    }

    function pow($base, $exponent)
    {
        return bcpow($base, $exponent);
    }

    function rand($n)
    {
        return BcMathUtils::bcrand($n);
    }
}
