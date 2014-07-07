<?php

namespace PhpEcc;

interface TheoryAdapter
{

    function modular_exp($base, $exponent, $modulus);

    function polynomial_reduce_mod($poly, $polymod, $p);

    function polynomial_multiply_mod($m1, $m2, $polymod, $p);

    function polynomial_exp_mod($base, $exponent, $polymod, $p);

    function jacobi($a, $n);

    function square_root_mod_prime($a, $p);

    function inverse_mod($a, $m);

    function gcd2($a, $b);

    function gcd($a);

    function lcm2($a, $b);

    function lcm($a);

    function factorization($n);

    function phi($n);

    function carmichael($n);

    function carmichael_of_factorized($f_list);

    function carmichael_of_ppower($pp);

    function order_mod($x, $m);

    function largest_factor_relatively_prime($a, $b);

    function kinda_order_mod($x, $m);

    function is_prime($n);

    function next_prime($starting_value);
}
