<?php

namespace Mdanter\Ecc\Math;


interface GmpMathInterface
{
    /**
     * Compares two numbers
     *
     * @param  resource|\GMP $first
     * @param  resource|\GMP $other
     * @return int        less than 0 if first is less than second, 0 if equal, greater than 0 if greater than.
     */
    public function cmp($first, $other);

    /**
     * @param resource|\GMP $first
     * @param resource|\GMP $other
     * @return bool
     */
    public function equals($first, $other);
    
    /**
     * Returns the remainder of a division
     *
     * @param  resource|\GMP $number
     * @param  resource|\GMP $modulus
     * @return resource|\GMP
     */
    public function mod($number, $modulus);

    /**
     * Adds two numbers
     *
     * @param  resource|\GMP $augend
     * @param  resource|\GMP $addend
     * @return resource|\GMP
     */
    public function add($augend, $addend);

    /**
     * Substract one number from another
     *
     * @param  resource|\GMP $minuend
     * @param  resource|\GMP $subtrahend
     * @return resource|\GMP
     */
    public function sub($minuend, $subtrahend);

    /**
     * Multiplies a number by another.
     *
     * @param  resource|\GMP $multiplier
     * @param  resource|\GMP $multiplicand
     * @return resource|\GMP
     */
    public function mul($multiplier, $multiplicand);

    /**
     * Divides a number by another.
     *
     * @param  resource|\GMP $dividend
     * @param  resource|\GMP $divisor
     * @return resource|\GMP
     */
    public function div($dividend, $divisor);

    /**
     * Raises a number to a power.
     *
     * @param  resource|\GMP $base     The number to raise.
     * @param  int|string $exponent The power to raise the number to.
     * @return resource|\GMP
     */
    public function pow($base, $exponent);

    /**
     * Performs a logical AND between two values.
     *
     * @param  resource|\GMP $first
     * @param  resource|\GMP $other
     * @return resource|\GMP
     */
    public function bitwiseAnd($first, $other);

    /**
     * Performs a logical XOR between two values.
     *
     * @param  resource|\GMP $first
     * @param  resource|\GMP $other
     * @return resource|\GMP
     */
    public function bitwiseXor($first, $other);

    /**
     * Shifts bits to the right
     * @param resource|\GMP        $number    Number to shift
     * @param int|string  $positions Number of positions to shift
     * @return \GMP
     */
    public function rightShift($number, $positions);

    /**
     * Shifts bits to the left
     * @param resource|\GMP       $number    Number to shift
     * @param int|string $positions Number of positions to shift
     * @return \GMP
     */
    public function leftShift($number, $positions);

    /**
     * Returns the string representation of a returned value.
     *
     * @param resource|\GMP $value
     * @return int|string
     */
    public function toString($value);

    /**
     * Converts an hexadecimal string to decimal.
     *
     * @param  string $hexString
     * @return int|string
     */
    public function hexDec($hexString);

    /**
     * Converts a decimal string to hexadecimal.
     *
     * @param  int|string $decString
     * @return int|string
     */
    public function decHex($decString);

    /**
     * Calculates the modular exponent of a number.
     *
     * @param resource|\GMP $base
     * @param resource|\GMP $exponent
     * @param resource|\GMP $modulus
     */
    public function powmod($base, $exponent, $modulus);

    /**
     * Checks whether a number is a prime.
     *
     * @param  int|string $n
     * @return boolean
     */
    public function isPrime($n);

    /**
     * Gets the next known prime that is greater than a given prime.
     *
     * @param  resource|\GMP $currentPrime
     * @return resource|\GMP
     */
    public function nextPrime($currentPrime);

    /**
     * @param resource|\GMP $a
     * @param resource|\GMP $m
     * @return \GMP
     */
    public function inverseMod($a, $m);

    /**
     * @param resource|\GMP $a
     * @param resource|\GMP $p
     * @return \GMP
     */
    public function jacobi($a, $p);

    /**
     * @param  resource|\GMP $x
     * @return string|null
     */
    public function intToString($x);

    /**
     *
     * @param  int|string $s
     * @return int|string
     */
    public function stringToInt($s);

    /**
     *
     * @param  resource|\GMP $m
     * @return int|string
     */
    public function digestInteger($m);

    /**
     * @param  resource|\GMP $a
     * @param  resource|\GMP $m
     * @return resource|\GMP
     */
    public function gcd2($a, $m);

    /**
     * @param $value
     * @param $fromBase
     * @param $toBase
     * @return int|string
     */
    public function baseConvert($value, $fromBase, $toBase);

    /**
     * @return NumberTheory
     */
    public function getNumberTheory();

    /**
     * @param resource|\GMP $modulus
     * @return ModularArithmetic
     */
    public function getModularArithmetic($modulus);
}
