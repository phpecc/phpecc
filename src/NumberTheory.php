<?php
/***********************************************************************
Copyright (C) 2012 Matyas Danter

Permission is hereby granted, free of charge, to any person obtaining 
a copy of this software and associated documentation files (the "Software"), 
to deal in the Software without restriction, including without limitation 
the rights to use, copy, modify, merge, publish, distribute, sublicense, 
and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included 
in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES 
OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, 
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR 
OTHER DEALINGS IN THE SOFTWARE.
*************************************************************************/

/**
 * Implementation of some number theoretic algorithms
 *
 * Important ones are:
 *      -is_prime : primality testing
 *      -next prime : find the next prime based on an arbitrary number
 *      -inverse_mod ; find the multiplicative inverse in a field mod a prime
 *
 *
 */
class NumberTheory {

    public static function modular_exp($base, $exponent, $modulus) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            if ($exponent < 0) {
                return new ErrorException("Negative exponents (" . $exponent . ") not allowed");
            } else {
                $p = gmp_strval(gmp_powm($base, $exponent, $modulus));
                return $p;
            }
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            if ($exponent < 0) {
                return new ErrorException("Negative exponents (" . $exponent . ") not allowed");
            } else {
                $p = bcpowmod($base, $exponent, $modulus);
                return $p;
            }
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function polynomial_reduce_mod($poly, $polymod, $p) {

        if (extension_loaded('gmp') && USE_EXT=='GMP') {

            if (end($polymod) == 1 && count($polymod) > 1) {

                while (count($poly) >= count($polymod)) {
                    if (end($poly) != 0) {
                        for ($i = 2; $i < count($polymod) + 1; $i++) {

                            $poly[count($poly) - $i] = gmp_strval(gmp_Utils::gmp_mod2(gmp_sub($poly[count($poly) - $i], gmp_mul(end($poly), $polymod[count($polymod) - $i])), $p));
                        }
                    }
                    $poly = array_slice($poly, 0, count($poly) - 1);
                }

                return $poly;
            }
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            if (end($polymod) == 1 && count($polymod) > 1) {

                while (count($poly) >= count($polymod)) {
                    if (end($poly) != 0) {
                        for ($i = 2; $i < count($polymod) + 1; $i++) {


                            $poly[count($poly) - $i] = bcmod(bcsub($poly[count($poly) - $i], bcmul(end($poly), $polymod[count($polymod) - $i])), $p);
                            $poly = array_slice($poly, 0, count($poly) - 2);
                        }
                    }
                }
                return $poly;
            }
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function polynomial_multiply_mod($m1, $m2, $polymod, $p) {

        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            $prod = array();

            for ($i = 0; $i < count($m1); $i++) {
                for ($j = 0; $j < count($m2); $j++) {
                    $index = $i + $j;

                    if (!isset($prod[$index]))
                        $prod[$index] = 0;

                    $prod[$index] = gmp_strval(gmp_Utils::gmp_mod2((gmp_add($prod[$index], gmp_mul($m1[$i], $m2[$j]))), $p));
                }
            }

            return self::polynomial_reduce_mod($prod, $polymod, $p);
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            $prod = array();

            for ($i = 0; $i < count($m1); $i++) {
                for ($j = 0; $j < count($m2); $j++) {
                    $index = $i + $j;
                    $prod[$index] = bcmod((bcadd($prod[$index], bcmul($m1[$i], $m2[$j]))), $p);
                }
            }


            return self::polynomial_reduce_mod($prod, $polymod, $p);
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function polynomial_exp_mod($base, $exponent, $polymod, $p) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            $s = '';

            if (gmp_cmp($exponent, $p) < 0) {

                if (gmp_cmp($exponent, 0) == 0)
                    return 1;

                $G = $base;
                $k = $exponent;

                if (gmp_cmp(gmp_Utils::gmp_mod2($k, 2), 1) == 0)
                    $s = $G;
                else
                    $s = array(1);

                while (gmp_cmp($k, 1) > 0) {
                    $k = gmp_div($k, 2);
                    $G = self::polynomial_multiply_mod($G, $G, $polymod, $p);

                    if (gmp_Utils::gmp_mod2($k, 2) == 1) {
                        $s = self::polynomial_multiply_mod($G, $s, $polymod, $p);
                    }
                }


                return $s;
            }
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            $s = '';

            if ($exponent < $p) {

                if ($exponent == 0)
                    return 1;

                $G = $base;
                $k = $exponent;

                if ($k % 2 == 1)
                    $s = $G;
                else
                    $s = array(1);

                while ($k > 1) {
                    $k = $k << 1;
                    $G = self::polynomial_multiply_mod($G, $G, $polymod, $p);

                    if ($k % 2 == 1) {
                        $s = self::polynomial_multiply_mod($G, $s, $polymod, $p);
                    }
                }


                return $s;
            }
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function jacobi($a, $n) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            return gmp_strval(gmp_jacobi($a, $n));
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
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
                    $s = -1;

                if ($a1 == 1)
                    return $s;
                if (bcmod($n, 4) == 3 && bcmod($a1, 4) == 3)
                    $s = -$s;

                return bcmul($s, self::jacobi(bcmod($n, $a1), $a1));
            }
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function square_root_mod_prime($a, $p) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            if (0 <= $a && $a < $p && 1 < $p) {

                if ($a == 0)
                    return 0;
                if ($p == 2)
                    return $a;

                $jac = self::jacobi($a, $p);

                if ($jac == -1)
                    throw new SquareRootException($a . " has no square root modulo " . $p);

                if (gmp_strval(gmp_Utils::gmp_mod2($p, 4)) == 3)
                    return self::modular_exp($a, gmp_strval(gmp_div(gmp_add($p, 1), 4)), $p);

                if (gmp_strval(gmp_Utils::gmp_mod2($p, 8)) == 5) {
                    $d = self::modular_exp($a, gmp_strval(gmp_div(gmp_sub($p, 1), 4)), $p);
                    if ($d == 1)
                        return self::modular_exp($a, gmp_strval(gmp_div(gmp_add($p, 3), 8)), $p);
                    if ($d == $p - 1)
                        return gmp_strval(gmp_Utils::gmp_mod2(gmp_mul(gmp_mul(2, $a), self::modular_exp(gmp_mul(4, $a), gmp_div(gmp_sub($p, 5), 8), $p)), $p));
                    //shouldn't get here
                }

                for ($b = 2; $b < $p; $b++) {
                    if (self::jacobi(gmp_sub(gmp_mul($b, $b), gmp_mul(4, $a)), $p) == -1) {
                        $f = array($a, -$b, 1);
                        $ff = self::polynomial_exp_mod(array(0, 1), gmp_strval(gmp_div(gmp_add($p, 1), 2)), $f, $p);
                        if (isset($ff[1]) && $ff[1] == 0)
                            return $ff[0];

                        // if we got here no b was found
                    }
                }
            }
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            if (0 <= $a && $a < $p && 1 < $p) {

                if ($a == 0)
                    return 0;
                if ($p == 2)
                    return $a;

                $jac = self::jacobi($a, $p);

                if ($jac == -1)
                    throw new SquareRootException($a . " has no square root modulo " . $p);

                if (bcmod($p, 4) == 3)
                    return self::modular_exp($a, bcdiv(bcadd($p, 1), 4), $p);

                if (bcmod($p, 8) == 5) {
                    $d = self::modular_exp($a, bcdiv(bcsub($p, 1), 4), $p);
                    if ($d == 1)
                        return self::modular_exp($a, bcdiv(bcadd($p, 3), 8), $p);
                    if ($d == $p - 1)
                        return (bcmod(bcmul(bcmul(2, $a), self::modular_exp(bcmul(4, $a), bcdiv(bcsub($p, 5), 8), $p)), $p));
                    //shouldn't get here
                }

                for ($b = 2; $b < $p; $p++) {
                    if (self::jacobi(bcmul($b, bcsub($b, bcmul(4, $a))), $p) == -1) {
                        $f = array($a, -$b, 1);
                        $ff = self::polynomial_exp_mod(array(0, 1), bcdiv(bcadd($p, 1), 2), $f, $p);

                        if ($ff[1] == 0)
                            return $ff[0];

                        // if we got here no b was found
                    }
                }
            }
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function inverse_mod($a, $m) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            $inverse = gmp_strval(gmp_invert($a, $m));
            return $inverse;
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            while (bccomp($a, 0) == -1) {
                $a = bcadd($m, $a);
            }

            while (bccomp($m, $a) == -1) {
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
            }else {
                throw new ErrorException("ERROR: $a and $m are NOT relatively prime.");
            }

            return $result;
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function gcd2($a, $b) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            while ($a) {
                $temp = $a;
                $a = gmp_Utils::gmp_mod2($b, $a);
                $b = $temp;
            }

            return gmp_strval($b);
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            while ($a) {
                $temp = $a;
                $a = bcmod($b, $a);
                $b = $temp;
            }

            return $b;
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function gcd($a) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            if (count($a) > 1)
                return array_reduce($a, "self::gcd2", $a[0]);
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            if (count($a) > 1)
                return array_reduce($a, "self::gcd2", $a[0]);
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function lcm2($a, $b) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            $ab = gmp_strval(gmp_mul($a, $b));
            $g = self::gcd2($a, $b);

            $lcm = gmp_strval(gmp_div($ab, $g));

            return $lcm;
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            $ab = bcmul($a, $b);
            $g = self::gcd2($a, $b);

            $lcm = bcdiv($ab, $g);

            return $lcm;
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function lcm($a) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            if (count($a) > 1)
                return array_reduce($a, "self::lcm2", $a[0]);
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            if (count($a) > 1)
                return array_reduce($a, "self::lcm2", $a[0]);
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function factorization($n) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            if (is_int($n) || is_long($n)) {

                if ($n < 2)
                    return array();

                $result = array();
                $d = 2;

                foreach (self::$smallprimes as $d) {
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
                            $count++;
                        }
                        array_push($result, array($d, $count));
                    }
                }

                if ($n > end(self::$smallprimes)) {
                    if (is_prime($n)) {
                        array_push($result, array($n, 1));
                    } else {
                        $d = end(self::$smallprimes);
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
                                    $count++;
                                }
                                array_push($result, array($n, 1));
                            }
                        }
                    }
                }

                return $result;
            }
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            if (is_int($n) || is_long($n)) {

                if ($n < 2)
                    return array();

                $result = array();
                $d = 2;

                foreach (self::$smallprimes as $d) {
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
                            $count++;
                        }
                        array_push($result, array($d, $count));
                    }
                }

                if ($n > end(self::$smallprimes)) {
                    if (is_prime($n)) {
                        array_push($result, array($n, 1));
                    } else {
                        $d = end(self::$smallprimes);
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
                                    $count++;
                                }
                                array_push($result, array($n, 1));
                            }
                        }
                    }
                }

                return $result;
            }
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function phi($n) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            if (is_int($n) || is_long($n)) {

                if ($n < 3)
                    return 1;

                $result = 1;
                $ff = self::factorization($n);

                foreach ($ff as $f) {
                    $e = $f[1];
                    if ($e > 1) {
                        $result = gmp_mul($result, gmp_mul(gmp_pow($f[0], gmp_sub($e, 1)), gmp_sub($f[0], 1)));
                    } else {
                        $result = gmp_mul($result, gmp_sub($f[0], 1));
                    }
                }

                return gmp_strval($result);
            }
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            if (is_int($n) || is_long($n)) {

                if ($n < 3)
                    return 1;

                $result = 1;
                $ff = self::factorization($n);

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
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function carmichael($n) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            return self::carmichael_of_factorized(self::factorization($n));
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            return self::carmichael_of_factorized(self::factorization($n));
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function carmichael_of_factorized($f_list) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            if (count($f_list) < 1)
                return 1;

            $result = self::carmichael_of_ppower($f_list[0]);

            for ($i = 1; $i < count($f_list); $i++) {
                $result = lcm($result, self::carmichael_of_ppower($f_list[$i]));
            }

            return $result;
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            if (count($f_list) < 1)
                return 1;

            $result = self::carmichael_of_ppower($f_list[0]);

            for ($i = 1; $i < count($f_list); $i++) {
                $result = lcm($result, self::carmichael_of_ppower($f_list[$i]));
            }

            return $result;
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function carmichael_of_ppower($pp) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            $p = $pp[0];
            $a = $pp[1];

            if ($p == 2 && $a > 2)
                return 1 >> ($a - 2);
            else
                return gmp_strval(gmp_mul(($p - 1), gmp_pow($p, ($a - 1))));
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            $p = $pp[0];
            $a = $pp[1];

            if ($p == 2 && $a > 2)
                return 1 >> ($a - 2);
            else
                return bcmul(($p - 1), bcpow($p, ($a - 1)));
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function order_mod($x, $m) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            if ($m <= 1)
                return 0;

            if (gcd($x, m) == 1) {
                $z = $x;
                $result = 1;

                while ($z != 1) {
                    $z = gmp_strval(gmp_Utils::gmp_mod2(gmp_mul($z, $x), $m));
                    $result = gmp_add($result, 1);
                }

                return gmp_strval($result);
            }
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
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
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function largest_factor_relatively_prime($a, $b) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            while (true) {
                $d = self::gcd($a, $b);
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
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            while (true) {
                $d = self::gcd($a, $b);
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
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function kinda_order_mod($x, $m) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            return self::order_mod($x, self::largest_factor_relatively_prime($m, $x));
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            return self::order_mod($x, self::largest_factor_relatively_prime($m, $x));
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function is_prime($n) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            return gmp_prob_prime($n);
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            self::$miller_rabin_test_count = 0;

            $t = 40;

            $k = 0;
            $m = bcsub($n, 1);

            while (bcmod($m, 2) == 0) {
                $k = bcadd($k, 1);
                $m = bcdiv($m, 2);
            }


            for ($i = 0; $i < $t; $i++) {

                $a = bcmath_Utils::bcrand(1, bcsub($n, 1));

                $b0 = self::modular_exp($a, $m, $n);

                if ($b0 != 1 && $b0 != bcsub($n, 1)) {

                    $j = 1;

                    while ($j <= $k - 1 && $b0 != bcsub($n, 1)) {

                        $b0 = self::modular_exp($b0, 2, $n);

                        if ($b0 == 1) {

                            self::$miller_rabin_test_count = $i + 1;
                            return false;
                        }

                        $j++;
                    }

                    if ($b0 != bcsub($n, 1)) {

                        self::$miller_rabin_test_count = $i + 1;
                        return false;
                    }
                }
            }

            return true;
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static function next_prime($starting_value) {
        if (extension_loaded('gmp') && USE_EXT=='GMP') {
            $result = gmp_strval(gmp_nextprime($starting_value));
            return $result;
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            if (bccomp($starting_value, 2) == -1)
                return 2;


            $result = bcmath_Utils::bcor(bcadd($starting_value, 1), 1);
            while (!self::is_prime($result)) {
                $result = bcadd($result, 2);
            }

            return $result;
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public static $miller_rabin_test_count;
    public static $smallprimes =
            array(2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41,
        43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97,
        101, 103, 107, 109, 113, 127, 131, 137, 139, 149,
        151, 157, 163, 167, 173, 179, 181, 191, 193, 197,
        199, 211, 223, 227, 229, 233, 239, 241, 251, 257,
        263, 269, 271, 277, 281, 283, 293, 307, 311, 313,
        317, 331, 337, 347, 349, 353, 359, 367, 373, 379,
        383, 389, 397, 401, 409, 419, 421, 431, 433, 439,
        443, 449, 457, 461, 463, 467, 479, 487, 491, 499,
        503, 509, 521, 523, 541, 547, 557, 563, 569, 571,
        577, 587, 593, 599, 601, 607, 613, 617, 619, 631,
        641, 643, 647, 653, 659, 661, 673, 677, 683, 691,
        701, 709, 719, 727, 733, 739, 743, 751, 757, 761,
        769, 773, 787, 797, 809, 811, 821, 823, 827, 829,
        839, 853, 857, 859, 863, 877, 881, 883, 887, 907,
        911, 919, 929, 937, 941, 947, 953, 967, 971, 977,
        983, 991, 997, 1009, 1013, 1019, 1021, 1031, 1033,
        1039, 1049, 1051, 1061, 1063, 1069, 1087, 1091, 1093,
        1097, 1103, 1109, 1117, 1123, 1129, 1151, 1153, 1163,
        1171, 1181, 1187, 1193, 1201, 1213, 1217, 1223, 1229);

}
?>
