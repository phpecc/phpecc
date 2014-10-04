<?php
namespace Mdanter\Ecc;

/**
 * *********************************************************************
 * Copyright (C) 2012 Matyas Danter
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES
 * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 * ***********************************************************************
 */

/**
 * Implementation of some number theoretic algorithms
 *
 * Important ones are:
 * -isPrime : primality testing
 * -next prime : find the next prime based on an arbitrary number
 * -inverseMod ; find the multiplicative inverse in a field mod a prime
 */
class NumberTheory
{

    /**
     * @var TheoryAdapter
     */
    private static $theory;

    public static function setTheoryAdapter(TheoryAdapter $adapter)
    {
        self::$theory = $adapter;
    }

    public static function modularExp($base, $exponent, $modulus)
    {
        return self::$theory->modularExp($base, $exponent, $modulus);
    }

    public static function polynomialReduceMod($poly, $polymod, $p)
    {
        return self::$theory->polynomialReduceMod($poly, $polymod, $p);
    }

    public static function polynomialMultiplyMod($m1, $m2, $polymod, $p)
    {
        return self::$theory->polynomialMultiplyMod($m1, $m2, $polymod, $p);
    }

    public static function polynomialExpMod($base, $exponent, $polymod, $p)
    {
        return self::$theory->polynomialExpMod($base, $exponent, $polymod, $p);
    }

    public static function jacobi($a, $n)
    {
        return self::$theory->jacobi($a, $n);
    }

    public static function squareRootModPrime($a, $p)
    {
        return self::$theory->squareRootModPrime($a, $p);
    }

    public static function inverseMod($a, $m)
    {
        return self::$theory->inverseMod($a, $m);
    }

    public static function gcd2($a, $b)
    {
        return self::$theory->gcd2($a, $b);
    }

    public static function gcd($a)
    {
        return self::$theory->gcd($a);
    }

    public static function lcm2($a, $b)
    {
        return self::$theory->lcm2($a, $b);
    }

    public static function lcm($a)
    {
        return self::$theory->lcm($a);
    }

    public static function factorization($n)
    {
        return self::$theory->factorization($n);
    }

    public static function phi($n)
    {
        return self::$theory->phi($n);
    }

    public static function carmichael($n)
    {
        return self::$theory->carmichael($n);
    }

    public static function carmichaelOfFactorized($f_list)
    {
        return self::$theory->carmichaelOfFactorized($f_list);
    }

    public static function carmichaelOfPpower($pp)
    {
        return self::$theory->carmichaelOfPpower($pp);
    }

    public static function orderMod($x, $m)
    {
        return self::$theory->orderMod($x, $m);
    }

    public static function largestFactorRelativelyPrime($a, $b)
    {
        return self::$theory->largestFactorRelativelyPrime($a, $b);
    }

    public static function kindaOrderMod($x, $m)
    {
        return self::$theory->kindaOrderMod($x, $m);
    }

    /**
     *
     * @todo Better detection of big primes with GMP (current mode is probabilistic)
     * @param string $n
     * @throws \RuntimeException
     * @return boolean
     */
    public static function isPrime($n)
    {
        return self::$theory->isPrime($n);
    }

    public static function nextPrime($starting_value)
    {
        return self::$theory->nextPrime($starting_value);
    }

    public static $smallprimes = array(2,3,5,7,11,13,17,19,23,29,31,37,41,43,47,53,59,61,67,71,73,79,83,89,97,101,103,107,109,113,127,131,137,139,149,151,157,163,167,173,179,181,191,193,197,199,211,223,227,229,233,239,241,251,257,263,269,271,277,281,283,293,307,311,313,317,331,337,347,349,353,359,367,373,379,383,389,397,401,409,419,421,431,433,439,443,449,457,461,463,467,479,487,491,499,503,509,521,523,541,547,557,563,569,571,577,587,593,599,601,607,613,617,619,631,641,643,647,653,659,661,673,
        677,683,691,701,709,719,727,733,739,743,751,757,761,769,773,787,797,809,811,821,823,827,829,839,853,857,859,863,877,881,883,887,907,911,919,929,937,941,947,953,967,971,977,983,991,997,1009,1013,1019,1021,1031,1033,1039,1049,1051,1061,1063,1069,1087,1091,1093,1097,1103,1109,1117,1123,1129,1151,1153,1163,1171,1181,1187,1193,1201,1213,1217,1223,1229);
}
