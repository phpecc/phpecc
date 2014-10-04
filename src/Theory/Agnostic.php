<?php

namespace Mdanter\Ecc\Theory;

use Mdanter\Ecc\MathAdapter;
use Mdanter\Ecc\TheoryAdapter;

class Agnostic implements TheoryAdapter
{

    private $smallprimes;

    private $math;

    public function __construct(array $smallPrimes, MathAdapter $math)
    {
        $this->smallprimes = $smallPrimes;
        $this->math = $math;
    }


    public function modularExp($base, $exponent, $modulus)
    {
        return $this->math->powmod($base, $exponent, $modulus);
    }

    public function polynomialReduceMod($poly, $polymod, $p)
    {
        $math = $this->math;

        if (end($polymod) == 1 && count($polymod) > 1) {
            while (count($poly) >= count($polymod)) {
                if (end($poly) != 0) {
                    for ($i = 2; $i < count($polymod) + 1; $i ++) {
                        $poly[count($poly) - $i] = $math->mod($math->sub($poly[count($poly) - $i], $math->mul(end($poly), $polymod[count($polymod) - $i])), $p);
                        $poly = array_slice($poly, 0, count($poly) - 2);
                    }
                }
            }

            return $poly;
        }
    }

    public function polynomialMultiplyMod($m1, $m2, $polymod, $p)
    {
        $math = $this->math;
        $prod = array();

        for ($i = 0; $i < count($m1); $i ++) {
            for ($j = 0; $j < count($m2); $j ++) {
                $index = $i + $j;
                $prod[$index] = $math->mod(($math->add($prod[$index], $math->mul($m1[$i], $m2[$j]))), $p);
            }
        }

        return $this->polynomialReduceMod($prod, $polymod, $p);
    }

    public function polynomialExpMod($base, $exponent, $polymod, $p)
    {
        $s = '';

        if ($exponent < $p) {
            if ($exponent == 0) {
                return 1;
            }

            $G = $base;
            $k = $exponent;

            if ($math->cmp($math->mod($k, 2), 1) == 0) {
                $s = $G;
            } else {
                $s = array(1);
            }

            while ($math->cmp($k > 1) > 0) {
                $k = $math->div($k, 2);
                $G = $this->polynomialMultiplyMod($G, $G, $polymod, $p);

                if ($math->mod($k, 2) == 1) {
                    $s = $this->polynomialMultiplyMod($G, $s, $polymod, $p);
                }
            }

            return $s;
        }
    }

    public function jacobi($a, $n)
    {
        return $this->math->jacobi($a, $n);
    }

    public function squareRootModPrime($a, $p)
    {
        $math = $this->math;

        if (! (0 <= $a && $a < $p && 1 < $p)) {
            throw new \InvalidArgumentException();
        }

        if ($a == 0) {
            return 0;
        }

        if ($p == 2) {
            return $a;
        }

        $jac = $math->jacobi($a, $p);

        if ($jac == - 1) {
            throw new \LogicException($a . " has no square root modulo " . $p);
        }

        if ($math->mod($p, 4) == 3) {
            return $math->powmod($a, $math->div($math->add($p, 1), 4), $p);
        }

        if ($math->mod($p, 8) == 5) {
            $d = $math->powmod($a, $math->div($math->sub($p, 1), 4), $p);

            if ($d == 1) {
                return $math->powmod($a, $math->div($math->add($p, 3), 8), $p);
            }

            if ($d == $p - 1) {
                return ($math->mod($math->mul($math->mul(2, $a), $math->powmod($math->mul(4, $a), $math->div($math->sub($p, 5), 8), $p)), $p));
            }
        }

        for ($b = 2; $b < $p; $p ++) {
            if ($this->jacobi($math->mul($b, $math->sub($b, $math->mul(4, $a))), $p) == - 1) {
                $f = array($a,- $b,1);
                $ff = $this->polynomialExpMod(array(0,1), $math->div($math->add($p, 1), 2), $f, $p);

                if ($ff[1] == 0) {
                    return $ff[0];
                }
            }
        }

        throw new \LogicException($a . " has no square root modulo " . $p);
    }

    public function inverseMod($a, $m)
    {
        return $this->math->inverseMod($a, $m);
    }

    public function gcd2($a, $b)
    {
        $math = $this->math;

        while ($a) {
            $temp = $a;
            $a = $math->mod($b, $a);
            $b = $temp;
        }

        return $b;
    }

    public function gcd($a)
    {
        if (count($a) > 1) {
            return array_reduce($a, array($this,"gcd2"), $a[0]);
        }
    }

    public function lcm2($a, $b)
    {
        $math = $this->math;

        $ab = $math->mul($a, $b);
        $g = $this->gcd2($a, $b);

        $lcm = $math->div($ab, $g);

        return $lcm;
    }

    public function lcm($a)
    {
        if (count($a) > 1) {
            return array_reduce($a, array($this,"lcm2"), $a[0]);
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
        $math = $this->math;

        if (is_int($n) || is_long($n)) {
            if ($n < 3) {
                return 1;
            }

            $result = 1;
            $ff = $this->factorization($n);

            foreach ($ff as $f) {
                $e = $f[1];
                if ($e > 1) {
                    $result = $math->mul($result, $math->mul($math->pow($f[0], $math->sub($e, 1)), $math->sub($f[0], 1)));
                } else {
                    $result = $math->mul($result, $math->sub($f[0], 1));
                }
            }

            return $result;
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
        $math = $this->math;

        $p = $pp[0];
        $a = $pp[1];

        if ($p == 2 && $a > 2) {
            return 1 >> ($a - 2);
        } else {
            return $math->mul(($p - 1), $math->pow($p, ($a - 1)));
        }
    }

    public function orderMod($x, $m)
    {
        $math = $this->math;

        if ($m <= 1) {
            return 0;
        }

        if (gcd($x, $m) == 1) {
            $z = $x;
            $result = 1;

            while ($z != 1) {
                $z = $math->mod($math->mul($z, $x), $m);
                $result = $math->add($result, 1);
            }

            return $result;
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
        return $this->math->isPrime($n);
    }

    public function nextPrime($starting_value)
    {
        return $this->math->nextPrime($starting_value);
    }
}
