<?php

namespace PhpEcc\Theory;

use PhpEcc\TheoryAdapter;
use PhpEcc\bcmath_Utils;

class Bc implements TheoryAdapter
{

    private $smallprimes;

    public function __construct(array $smallPrimes)
    {
        if (! extension_loaded('bcmath')) {
            throw new \ErrorException('BCMath extension is not loaded.');
        }

        $this->smallprimes = $smallPrimes;
    }

    public function modular_exp($base, $exponent, $modulus)
    {
        if ($exponent < 0) {
            return new ErrorException("Negative exponents (" . $exponent . ") not allowed");
        } else {
            $p = bcpowmod($base, $exponent, $modulus);
            return $p;
        }
    }

    public function polynomial_reduce_mod($poly, $polymod, $p)
    {
        if (end($polymod) == 1 && count($polymod) > 1) {

            while (count($poly) >= count($polymod)) {
                if (end($poly) != 0) {
                    for ($i = 2; $i < count($polymod) + 1; $i ++) {

                        $poly[count($poly) - $i] = bcmod(bcsub($poly[count($poly) - $i], bcmul(end($poly), $polymod[count($polymod) - $i])), $p);
                        $poly = array_slice($poly, 0, count($poly) - 2);
                    }
                }
            }
            return $poly;
        }
    }

    public function polynomial_multiply_mod($m1, $m2, $polymod, $p)
    {
        $prod = array();

        for ($i = 0; $i < count($m1); $i ++) {
            for ($j = 0; $j < count($m2); $j ++) {
                $index = $i + $j;
                $prod[$index] = bcmod((bcadd($prod[$index], bcmul($m1[$i], $m2[$j]))), $p);
            }
        }

        return $this->polynomial_reduce_mod($prod, $polymod, $p);
    }

    public function polynomial_exp_mod($base, $exponent, $polymod, $p)
    {
        $s = '';

        if ($exponent < $p) {

            if ($exponent == 0)
                return 1;

            $G = $base;
            $k = $exponent;

            if ($k % 2 == 1)
                $s = $G;
            else
                $s = array(
                    1
                );

            while ($k > 1) {
                $k = $k << 1;
                $G = $this->polynomial_multiply_mod($G, $G, $polymod, $p);

                if ($k % 2 == 1) {
                    $s = $this->polynomial_multiply_mod($G, $s, $polymod, $p);
                }
            }

            return $s;
        }
    }

    public function jacobi($a, $n)
    {
        if ($n >= 3 && $n % 2 == 1) {
            $a = bcmod($a, $n);

            if ($a == 0)
                return 0;
            if ($a == 1)
                return 1;

            $a1 = $a;
            $e = 0;

            while (bcmod($a1, 2) == 0) {
                $a1 = bcdiv($a1, 2);
                $e = bcadd($e, 1);
            }

            if (bcmod($e, 2) == 0 || bcmod($n, 8) == 1 || bcmod($n, 8) == 7)
                $s = 1;
            else
                $s = - 1;

            if ($a1 == 1)
                return $s;
            if (bcmod($n, 4) == 3 && bcmod($a1, 4) == 3)
                $s = - $s;

            return bcmul($s, $this->jacobi(bcmod($n, $a1), $a1));
        }
    }

    public function square_root_mod_prime($a, $p)
    {
        if (0 <= $a && $a < $p && 1 < $p) {

            if ($a == 0)
                return 0;
            if ($p == 2)
                return $a;

            $jac = $this->jacobi($a, $p);

            if ($jac == - 1)
                throw new SquareRootException($a . " has no square root modulo " . $p);

            if (bcmod($p, 4) == 3)
                return $this->modular_exp($a, bcdiv(bcadd($p, 1), 4), $p);

            if (bcmod($p, 8) == 5) {
                $d = $this->modular_exp($a, bcdiv(bcsub($p, 1), 4), $p);
                if ($d == 1)
                    return $this->modular_exp($a, bcdiv(bcadd($p, 3), 8), $p);
                if ($d == $p - 1)
                    return (bcmod(bcmul(bcmul(2, $a), $this->modular_exp(bcmul(4, $a), bcdiv(bcsub($p, 5), 8), $p)), $p));
                // shouldn't get here
            }

            for ($b = 2; $b < $p; $p ++) {
                if ($this->jacobi(bcmul($b, bcsub($b, bcmul(4, $a))), $p) == - 1) {
                    $f = array(
                        $a,
                        - $b,
                        1
                    );
                    $ff = $this->polynomial_exp_mod(array(
                        0,
                        1
                    ), bcdiv(bcadd($p, 1), 2), $f, $p);

                    if ($ff[1] == 0)
                        return $ff[0];

                    // if we got here no b was found
                }
            }
        }
    }

    public function inverse_mod($a, $m)
    {
        while (bccomp($a, 0) == - 1) {
            $a = bcadd($m, $a);
        }

        while (bccomp($m, $a) == - 1) {
            $a = bcmod($a, $m);
        }

        $c = $a;
        $d = $m;
        $uc = 1;
        $vc = 0;
        $ud = 0;
        $vd = 1;

        while (bccomp($c, 0) != 0) {
            $temp1 = $c;
            $q = bcdiv($d, $c, 0);

            $c = bcmod($d, $c);
            $d = $temp1;

            $temp2 = $uc;
            $temp3 = $vc;
            $uc = bcsub($ud, bcmul($q, $uc));
            $vc = bcsub($vd, bcmul($q, $vc));
            $ud = $temp2;
            $vd = $temp3;
        }

        $result = '';

        if (bccomp($d, 1) == 0) {
            if (bccomp($ud, 0) == 1)
                $result = $ud;
            else
                $result = bcadd($ud, $m);
        } else {
            throw new ErrorException("ERROR: $a and $m are NOT relatively prime.");
        }

        return $result;
    }

    public function gcd2($a, $b)
    {
        while ($a) {
            $temp = $a;
            $a = bcmod($b, $a);
            $b = $temp;
        }

        return $b;
    }

    public function gcd($a)
    {
        if (count($a) > 1) {
            return array_reduce($a, array(
                $this,
                "gcd2"
            ), $a[0]);
        }
    }

    public function lcm2($a, $b)
    {
        $ab = bcmul($a, $b);
        $g = $this->gcd2($a, $b);

        $lcm = bcdiv($ab, $g);

        return $lcm;
    }

    public function lcm($a)
    {
        if (count($a) > 1) {
            return array_reduce($a, array(
                $this,
                "lcm2"
            ), $a[0]);
        }
    }

    public function factorization($n)
    {
        if (is_int($n) || is_long($n)) {

            if ($n < 2)
                return array();

            $result = array();
            $d = 2;

            foreach ($this->smallprimes as $d) {
                if ($d > $n)
                    break;
                $q = $n / $d;
                $r = $n % $d;
                if ($r == 0) {
                    $count = 1;
                    while ($d <= $n) {
                        $n = $q;
                        $q = $n / $d;
                        $r = $n % $d;
                        if ($r != 0)
                            break;
                        $count ++;
                    }
                    array_push($result, array(
                        $d,
                        $count
                    ));
                }
            }

            if ($n > end($this->smallprimes)) {
                if (is_prime($n)) {
                    array_push($result, array(
                        $n,
                        1
                    ));
                } else {
                    $d = end($this->smallprimes);
                    while (true) {
                        $d += 2;
                        $q = $n / $d;
                        $r = $n % $d;
                        if ($q < $d)
                            break;
                        if ($r == 0) {
                            $count = 1;
                            $n = $q;
                            while ($d <= n) {
                                $q = $n / $d;
                                $r = $n % $d;
                                if ($r != 0)
                                    break;
                                $n = $q;
                                $count ++;
                            }
                            array_push($result, array(
                                $n,
                                1
                            ));
                        }
                    }
                }
            }

            return $result;
        }
    }

    public function phi($n)
    {
        if (is_int($n) || is_long($n)) {

            if ($n < 3)
                return 1;

            $result = 1;
            $ff = $this->factorization($n);

            foreach ($ff as $f) {
                $e = $f[1];
                if ($e > 1) {
                    $result = bcmul($result, bcmul(bcpow($f[0], bcsub($e, 1)), bcsub($f[0], 1)));
                } else {
                    $result = bcmul($result, bcsub($f[0], 1));
                }
            }

            return $result;
        }
    }

    public function carmichael($n)
    {
        return $this->carmichael_of_factorized($this->factorization($n));
    }

    public function carmichael_of_factorized($f_list)
    {
        if (count($f_list) < 1)
            return 1;

        $result = $this->carmichael_of_ppower($f_list[0]);

        for ($i = 1; $i < count($f_list); $i ++) {
            $result = lcm($result, $this->carmichael_of_ppower($f_list[$i]));
        }

        return $result;
    }

    public function carmichael_of_ppower($pp)
    {
        $p = $pp[0];
        $a = $pp[1];

        if ($p == 2 && $a > 2)
            return 1 >> ($a - 2);
        else
            return bcmul(($p - 1), bcpow($p, ($a - 1)));
    }

    public function order_mod($x, $m)
    {
        if ($m <= 1)
            return 0;

        if (gcd($x, m) == 1) {
            $z = $x;
            $result = 1;

            while ($z != 1) {
                $z = bcmod(bcmul($z, $x), $m);
                $result = bcadd($result, 1);
            }

            return $result;
        }
    }

    public function largest_factor_relatively_prime($a, $b)
    {
        while (true) {
            $d = $this->gcd($a, $b);
            if ($d <= 1)
                break;

            $b = $d;
            while (true) {
                $q = $a / $d;
                $r = $a % $d;
                if ($r > 0)
                    break;
                $a = $q;
            }
        }

        return $a;
    }

    public function kinda_order_mod($x, $m)
    {
        return $this->order_mod($x, $this->largest_factor_relatively_prime($m, $x));
    }

    public function is_prime($n)
    {
        $miller_rabin_test_count = 0;

        $t = 40;

        $k = 0;
        $m = bcsub($n, 1);

        while (bcmod($m, 2) == 0) {
            $k = bcadd($k, 1);
            $m = bcdiv($m, 2);
        }

        for ($i = 0; $i < $t; $i ++) {

            $a = bcmath_Utils::bcrand(1, bcsub($n, 1));

            $b0 = $this->modular_exp($a, $m, $n);

            if ($b0 != 1 && $b0 != bcsub($n, 1)) {

                $j = 1;

                while ($j <= $k - 1 && $b0 != bcsub($n, 1)) {

                    $b0 = $this->modular_exp($b0, 2, $n);

                    if ($b0 == 1) {

                        $miller_rabin_test_count = $i + 1;
                        return false;
                    }

                    $j ++;
                }

                if ($b0 != bcsub($n, 1)) {

                    $miller_rabin_test_count = $i + 1;
                    return false;
                }
            }
        }

        return true;
    }

    public function next_prime($starting_value)
    {
        if (bccomp($starting_value, 2) == - 1)
            return 2;

        $result = bcmath_Utils::bcor(bcadd($starting_value, 1), 1);
        while (! $this->is_prime($result)) {
            $result = bcadd($result, 2);
        }

        return $result;
    }
}
