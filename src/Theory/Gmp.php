<?php
namespace Mdanter\Ecc\Theory;

use Mdanter\Ecc\TheoryAdapter;
use Mdanter\Ecc\Math\GmpUtils;

class Gmp implements TheoryAdapter
{

    private $smallprimes;

    public function __construct(array $smallPrimes)
    {
        if (! extension_loaded('gmp')) {
            throw new \RuntimeException('GMP extension is not loaded.');
        }

        $this->smallprimes = $smallPrimes;
    }

    public function modularExp($base, $exponent, $modulus)
    {
        if ($exponent < 0) {
            throw new \RuntimeException("Negative exponents (" . $exponent . ") not allowed");
        }

        $p = gmp_strval(gmp_powm($base, $exponent, $modulus));

        return $p;
    }

    public function polynomialReduceMod($poly, $polymod, $p)
    {
        if (end($polymod) == 1 && count($polymod) > 1) {

            while (count($poly) >= count($polymod)) {
                if (end($poly) != 0) {
                    for ($i = 2; $i < count($polymod) + 1; $i ++) {
                        $poly[count($poly) - $i] = gmp_strval(GmpUtils::gmpMod2(gmp_sub($poly[count($poly) - $i], gmp_mul(end($poly), $polymod[count($polymod) - $i])), $p));
                    }
                }
                $poly = array_slice($poly, 0, count($poly) - 1);
            }

            return $poly;
        }
    }

    public function polynomialMultiplyMod($m1, $m2, $polymod, $p)
    {
        $prod = array();

        for ($i = 0; $i < count($m1); $i ++) {
            for ($j = 0; $j < count($m2); $j ++) {
                $index = $i + $j;

                if (! isset($prod[$index])) {
                    $prod[$index] = 0;
                }

                $prod[$index] = gmp_strval(GmpUtils::gmpMod2((gmp_add($prod[$index], gmp_mul($m1[$i], $m2[$j]))), $p));
            }
        }

        return $this->polynomialReduceMod($prod, $polymod, $p);
    }

    public function polynomialExpMod($base, $exponent, $polymod, $p)
    {
        $s = '';

        if (gmp_cmp($exponent, $p) < 0) {

            if (gmp_cmp($exponent, 0) == 0) {
                return 1;
            }

            $G = $base;
            $k = $exponent;

            if (gmp_cmp(GmpUtils::gmpMod2($k, 2), 1) == 0) {
                $s = $G;
            } else {
                $s = array(1);
            }

            while (gmp_cmp($k, 1) > 0) {
                $k = gmp_div($k, 2);
                $G = $this->polynomialMultiplyMod($G, $G, $polymod, $p);

                if (GmpUtils::gmpMod2($k, 2) == 1) {
                    $s = $this->polynomialMultiplyMod($G, $s, $polymod, $p);
                }
            }

            return $s;
        }
    }

    public function jacobi($a, $n)
    {
        return gmp_strval(gmp_jacobi($a, $n));
    }

    public function squareRootModPrime($a, $p)
    {
        if (0 <= $a && $a < $p && 1 < $p) {

            if ($a == 0) {
                return 0;
            }

            if ($p == 2) {
                return $a;
            }

            $jac = $this->jacobi($a, $p);

            if ($jac == - 1) {
                throw new \LogicException($a . " has no square root modulo " . $p);
            }

            if (gmp_strval(GmpUtils::gmpMod2($p, 4)) == 3) {
                return $this->modularExp($a, gmp_strval(gmp_div(gmp_add($p, 1), 4)), $p);
            }

            if (gmp_strval(GmpUtils::gmpMod2($p, 8)) == 5) {
                $d = $this->modularExp($a, gmp_strval(gmp_div(gmp_sub($p, 1), 4)), $p);

                if ($d == 1) {
                    return $this->modularExp($a, gmp_strval(gmp_div(gmp_add($p, 3), 8)), $p);
                }

                if ($d == $p - 1) {
                    return gmp_strval(GmpUtils::gmpMod2(gmp_mul(gmp_mul(2, $a), $this->modularExp(gmp_mul(4, $a), gmp_div(gmp_sub($p, 5), 8), $p)), $p));
                }

                // shouldn't get here
                // @todo throw \RuntimeException
            }

            for ($b = 2; $b < $p; $b ++) {
                if ($this->jacobi(gmp_sub(gmp_mul($b, $b), gmp_mul(4, $a)), $p) == - 1) {
                    $f = array($a,- $b,1);
                    $ff = $this->polynomialExpMod(array(0,1), gmp_strval(gmp_div(gmp_add($p, 1), 2)), $f, $p);

                    if (isset($ff[1]) && $ff[1] == 0) {
                        return $ff[0];
                    }

                    // if we got here no b was found
                }
            }
        }
    }

    public function inverseMod($a, $m)
    {
        $inverse = gmp_strval(gmp_invert($a, $m));
        return $inverse;
    }

    public function gcd2($a, $b)
    {
        while ($a) {
            $temp = $a;
            $a = GmpUtils::gmpMod2($b, $a);
            $b = $temp;
        }

        return gmp_strval($b);
    }

    public function gcd($a)
    {
        if (count($a) > 1) {
            return array_reduce($a, "$this->gcd2", $a[0]);
        }
    }

    public function lcm2($a, $b)
    {
        $ab = gmp_strval(gmp_mul($a, $b));
        $g = $this->gcd2($a, $b);

        $lcm = gmp_strval(gmp_div($ab, $g));

        return $lcm;
    }

    public function lcm($a)
    {
        if (count($a) > 1) {
            return array_reduce($a, "$this->lcm2", $a[0]);
        }
    }

    public function factorization($n)
    {
        if (is_int($n) || is_long($n)) {

            if ($n < 2) {
                return array();
            }

            $result = array();
            $d = 2;

            foreach ($this->smallprimes as $d) {
                if ($d > $n) {
                    break;
                }

                $q = $n / $d;
                $r = $n % $d;

                if ($r == 0) {
                    $count = 1;

                    while ($d <= $n) {
                        $n = $q;
                        $q = $n / $d;
                        $r = $n % $d;

                        if ($r != 0) {
                            break;
                        }

                        $count ++;
                    }

                    array_push($result, array($d,$count));
                }
            }

            if ($n > end($this->smallprimes)) {
                if ($this->isPrime($n)) {
                    array_push($result, array($n,1));
                } else {
                    $d = end($this->smallprimes);

                    while (true) {
                        $d += 2;
                        $q = $n / $d;
                        $r = $n % $d;

                        if ($q < $d) {
                            break;
                        }

                        if ($r == 0) {
                            $count = 1;
                            $n = $q;

                            while ($d <= $n) {
                                $q = $n / $d;
                                $r = $n % $d;

                                if ($r != 0) {
                                    break;
                                }

                                $n = $q;
                                $count ++;
                            }

                            array_push($result, array($n,1));
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
            if ($n < 3) {
                return 1;
            }

            $result = 1;
            $ff = $this->factorization($n);

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
    }

    public function carmichael($n)
    {
        return $this->carmichaelOfFactorized($this->factorization($n));
    }

    public function carmichaelOfFactorized($f_list)
    {
        if (count($f_list) < 1) {
            return 1;
        }

        $result = $this->carmichaelOfPpower($f_list[0]);

        for ($i = 1; $i < count($f_list); $i ++) {
            $result = lcm($result, $this->carmichaelOfPpower($f_list[$i]));
        }

        return $result;
    }

    public function carmichaelOfPpower($pp)
    {
        $p = $pp[0];
        $a = $pp[1];

        if ($p == 2 && $a > 2) {
            return 1 >> ($a - 2);
        }
        else {
            return gmp_strval(gmp_mul(($p - 1), gmp_pow($p, ($a - 1))));
        }
    }

    public function orderMod($x, $m)
    {
        if ($m <= 1) {
            return 0;
        }

        if (gcd($x, $m) == 1) {
            $z = $x;
            $result = 1;

            while ($z != 1) {
                $z = gmp_strval(GmpUtils::gmpMod2(gmp_mul($z, $x), $m));
                $result = gmp_add($result, 1);
            }

            return gmp_strval($result);
        }
    }

    public function largestFactorRelativelyPrime($a, $b)
    {
        while (true) {
            $d = $this->gcd($a, $b);

            if ($d <= 1) {
                break;
            }

            $b = $d;

            while (true) {
                $q = $a / $d;
                $r = $a % $d;

                if ($r > 0) {
                    break;
                }

                $a = $q;
            }
        }

        return $a;
    }

    public function kindaOrderMod($x, $m)
    {
        return $this->orderMod($x, $this->largestFactorRelativelyPrime($m, $x));
    }

    public function isPrime($n)
    {
        $prob = gmp_prob_prime($n);

        if ($prob > 0) {
            return true;
        }

        return false;
    }

    public function nextPrime($starting_value)
    {
        $result = gmp_strval(gmp_nextprime($starting_value));
        return $result;
    }
}
