<?php

namespace Mdanter\Ecc;

interface MathAdapter
{
    function cmp($first, $other);

    function mod($number, $modulus);

    function add($augend, $addend);

    function sub($minuend, $subtrahend);

    function mul($multiplier, $multiplicand);

    /**
     * @param integer $divisor
     */
    function div($dividend, $divisor);

    /**
     * @param integer $exponent
     */
    function pow($base, $exponent);

    /**
     * Generates a random integer between 0 (inclusive) and $n (inclusive).
     * @param int $n max
     */
    function rand($n);

    function bitwiseAnd($first, $other);

    function toString($value);

    /**
     * @param string $hexString
     */
    function hexDec($hexString);

    function decHex($decString);

    function powmod($base, $exponent, $modulus);

    /**
     * @return boolean
     */
    function isPrime($n);

    function nextPrime($currentPrime);

    function inverseMod($a, $m);

    function jacobi($a, $p);

    /**
     * @return string|null
     */
    function intToString($x);

    function stringToInt($s);

    function digestInteger($m);

    function gcd2($a, $m);
}
