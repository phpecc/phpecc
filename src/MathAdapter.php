<?php

namespace Mdanter\Ecc;

interface MathAdapter
{
    function cmp($first, $other);

    function mod($number, $modulus);

    function add($augend, $addend);

    function sub($minuend, $subtrahend);

    function mul($multiplier, $multiplicand);

    function pow($base, $exponent);

    function rand($n);
}
