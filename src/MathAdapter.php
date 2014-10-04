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

    function hexDec($hexString);

    function decHex($decString);

    function powmod($base, $exponent, $modulus);

    function isPrime($n);

    function nextPrime($currentPrime);

    function inverseMod($a, $m);

    function jacobi($a, $p);

    function intToString($x);

    function stringToInt($s);

    function digestInteger($m);
}
