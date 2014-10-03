<?php

namespace Mdanter\Ecc;

interface MathAdapter
{
    function cmp($first, $other);

    function mod($number, $modulus);

    function add($augend, $addend);

    function sub($minuend, $subtrahend);

    function mul($multiplier, $multiplicand);

    function div($dividend, $divisor);

    function pow($base, $exponent);

    function rand($n);

    function bitwiseAnd($first, $other);

    function toString($value);
}
