<?php

namespace Mdanter\Ecc;

interface TheoryAdapter
{

    function modularExp($base, $exponent, $modulus);

    function polynomialReduceMod($poly, $polymod, $p);

    function polynomialMultiplyMod($m1, $m2, $polymod, $p);

    function polynomialExpMod($base, $exponent, $polymod, $p);

    function jacobi($a, $n);

    function squareRootModPrime($a, $p);

    function inverseMod($a, $m);

    function gcd2($a, $b);

    function gcd($a);

    function lcm2($a, $b);

    function lcm($a);

    function factorization($n);

    function phi($n);

    function carmichael($n);

    function carmichaelOfFactorized($f_list);

    function carmichaelOfPpower($pp);

    function orderMod($x, $m);

    function largestFactorRelativelyPrime($a, $b);

    function kindaOrderMod($x, $m);

    function isPrime($n);

    function nextPrime($starting_value);
}
