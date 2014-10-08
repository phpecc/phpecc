<?php

namespace Mdanter\Ecc;

interface MathAdapter
{
    function cmp($first, $other);

    function mod($number, $modulus);

    function add($augend, $addend);

    function sub($minuend, $subtrahend);

    /**
     * Multiplies a number by another.
     *
     * @param int|string $multiplier
     * @param int|string $multiplicand
     * @return int|string
     */
    function mul($multiplier, $multiplicand);

    /**
     * Divides a number by another.
     *
     * @param number $divisor
     * @return int|string
     */
    function div($dividend, $divisor);

    /**
     * Raises a number to a power.
     *
     * @param int|string $base The number to raise.
     * @param int|string $exponent The power to raise the number to.
     * @return int|string
     */
    function pow($base, $exponent);

    /**
     * Generates a random integer between 0 (inclusive) and $n (inclusive).
     *
     * @param int|string $n Maximum value to return.
     * @return int|stringeger
     */
    function rand($n);

    /**
     * Performs a logical AND between two values.
     *
     * @param int|string $first
     * @param int|string $other
     * @return int|string
     */
    function bitwiseAnd($first, $other);

    /**
     * Returns the string representation of a returned value.
     *
     * @param int|string $value
     */
    function toString($value);

    /**
     * Converts an hexadecimal string to decimal.
     *
     * @param string $hexString
     * @return int|string
     */
    function hexDec($hexString);

    /**
     * Converts a decimal string to hexadecimal.
     *
     * @param int|string $decString
     * @return int|string
     */
    function decHex($decString);

    /**
     * Calculates the modular exponent of a number.
     *
     * @param int|string $base
     * @param int|string $exponent
     * @param int|string $modulus
     */
    function powmod($base, $exponent, $modulus);

    /**
     * Checks whether a number is a prime.
     *
     * @param int|string $n
     * @return boolean
     */
    function isPrime($n);

    /**
     * Gets the next known prime that is greater than a given prime.
     *
     * @param int|string $currentPrime
     * @return int|string
     */
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
