<?php

namespace Mdanter\Ecc\Math;

interface GmpMathInterface
{
    /**
     * Compares two numbers
     *
     * @param  \GMP $first
     * @param  \GMP $other
     * @return int        less than 0 if first is less than second, 0 if equal, greater than 0 if greater than.
     */
    public function cmp(\GMP $first, \GMP $other);

    /**
     * @param \GMP $first
     * @param \GMP $other
     * @return bool
     */
    public function equals(\GMP $first, \GMP $other);
    
    /**
     * Returns the remainder of a division
     *
     * @param  \GMP $number
     * @param  \GMP $modulus
     * @return \GMP
     */
    public function mod(\GMP $number, \GMP $modulus);

    /**
     * Adds two numbers
     *
     * @param  \GMP $augend
     * @param  \GMP $addend
     * @return \GMP
     */
    public function add(\GMP $augend, \GMP $addend);

    /**
     * Substract one number from another
     *
     * @param  \GMP $minuend
     * @param  \GMP $subtrahend
     * @return \GMP
     */
    public function sub(\GMP $minuend, \GMP $subtrahend);

    /**
     * Multiplies a number by another.
     *
     * @param  \GMP $multiplier
     * @param  \GMP $multiplicand
     * @return \GMP
     */
    public function mul(\GMP $multiplier, \GMP $multiplicand);

    /**
     * Divides a number by another.
     *
     * @param  \GMP $dividend
     * @param  \GMP $divisor
     * @return \GMP
     */
    public function div(\GMP $dividend, \GMP $divisor);

    /**
     * Raises a number to a power.
     *
     * @param  \GMP $base     The number to raise.
     * @param  int $exponent The power to raise the number to.
     * @return \GMP
     */
    public function pow(\GMP $base, $exponent);

    /**
     * Performs a logical AND between two values.
     *
     * @param  \GMP $first
     * @param  \GMP $other
     * @return \GMP
     */
    public function bitwiseAnd(\GMP $first, \GMP $other);

    /**
     * Performs a logical XOR between two values.
     *
     * @param  \GMP $first
     * @param  \GMP $other
     * @return \GMP
     */
    public function bitwiseXor(\GMP $first, \GMP $other);

    /**
     * Shifts bits to the right
     * @param \GMP        $number    Number to shift
     * @param int  $positions Number of positions to shift
     * @return \GMP
     */
    public function rightShift(\GMP $number, $positions);

    /**
     * Shifts bits to the left
     * @param \GMP       $number    Number to shift
     * @param int $positions Number of positions to shift
     * @return \GMP
     */
    public function leftShift(\GMP $number, $positions);

    /**
     * Returns the string representation of a returned value.
     *
     * @param \GMP $value
     * @return int|string
     */
    public function toString(\GMP $value);

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
     * @param \GMP $base
     * @param \GMP $exponent
     * @param \GMP $modulus
     */
    public function powmod(\GMP $base, \GMP $exponent, \GMP $modulus);

    /**
     * Checks whether a number is a prime.
     *
     * @param  \GMP $n
     * @return boolean
     */
    public function isPrime(\GMP $n);

    /**
     * Gets the next known prime that is greater than a given prime.
     *
     * @param  \GMP $currentPrime
     * @return \GMP
     */
    public function nextPrime(\GMP $currentPrime);

    /**
     * @param \GMP $a
     * @param \GMP $m
     * @return \GMP
     */
    public function inverseMod(\GMP $a, \GMP $m);

    /**
     * @param \GMP $a
     * @param \GMP $p
     * @return int
     */
    public function jacobi(\GMP $a, \GMP $p);

    /**
     * @param  \GMP $x
     * @return string|null
     */
    public function intToString(\GMP $x);

    /**
     *
     * @param  int|string $s
     * @return \GMP
     */
    public function stringToInt($s);

    /**
     *
     * @param  \GMP $m
     * @return \GMP
     */
    public function digestInteger(\GMP $m);

    /**
     * @param  \GMP $a
     * @param  \GMP $m
     * @return \GMP
     */
    public function gcd2(\GMP $a, \GMP $m);

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
     * @param \GMP $modulus
     * @return ModularArithmetic
     */
    public function getModularArithmetic(\GMP $modulus);
}
