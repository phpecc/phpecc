<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\MathAdapter;
use Mdanter\Ecc\NumberTheory;

class BcMath implements MathAdapter
{

    function cmp($first, $other)
    {
        return bccomp($first, $other);
    }

    function mod($number, $modulus)
    {
        return bcmod($number, $modulus);
    }

    function add($augend, $addend)
    {
        return bcadd($augend, $addend);
    }

    function sub($minuend, $subtrahend)
    {
        return bcsub($minuend, $subtrahend);
    }

    function mul($multiplier, $multiplicand)
    {
        return bcmul($multiplier, $multiplicand);
    }

    function div($dividend, $divisor)
    {
        return bcdiv($dividend, $divisor);
    }

    function pow($base, $exponent)
    {
        return bcpow($base, $exponent);
    }

    function rand($n)
    {
        return BcMathUtils::bcrand($n);
    }

    function bitwiseAnd($first, $other)
    {
        return BcMathUtils::bcand($first, $other);
    }

    function toString($value)
    {
        return (string)$value;
    }

    public function hexDec($hex)
    {
        return BcMathUtils::bchexdec($hex);
    }

    public function decHex($dec)
    {
        return BcMathUtils::bcdechex($dec);
    }

    public function powmod($base, $exponent, $modulus)
    {
        if ($exponent < 0) {
            throw new \InvalidArgumentException("Negative exponents ($exponent) not allowed.");
        }

        return bcpowmod($base, $exponent, $modulus);
    }

    public function isPrime($n)
    {
        $t = 40;
        $k = 0;
        $m = $this->sub($n, 1);

        while ($this->mod($m, 2) == 0) {
            $k = $this->add($k, 1);
            $m = $this->div($m, 2);
        }

        for ($i = 0; $i < $t; $i ++) {
            $a = BcMathUtils::bcrand(1, bcsub($n, 1));
            $b0 = $this->powmod($a, $m, $n);

            if ($b0 != 1 && $b0 != $this->sub($n, 1)) {
                $j = 1;

                while ($j <= $k - 1 && $b0 != $this->sub($n, 1)) {
                    $b0 = $this->powmod($b0, 2, $n);

                    if ($b0 == 1) {
                        return false;
                    }

                    $j ++;
                }

                if ($b0 != bcsub($n, 1)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function nextPrime($starting_value)
    {
        if (bccomp($starting_value, 2) == - 1) {
            return 2;
        }

        $result = BcMathUtils::bcor(bcadd($starting_value, 1), 1);

        while (! $this->isPrime($result)) {
            $result = bcadd($result, 2);
        }

        return $result;
    }

    public function inverseMod($a, $m)
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

        if (bccomp($d, 1) != 0) {
            throw new \RuntimeException("ERROR: $a and $m are NOT relatively prime.");
        }

        $result = bcadd($ud, $m);

        if (bccomp($ud, 0) == 1) {
            $result = $ud;
        }

        return $result;
    }

    public function jacobi($a, $n)
    {
        if ($n >= 3 && $n % 2 == 1) {
            $a = bcmod($a, $n);

            if ($a == 0) {
                return 0;
            }

            if ($a == 1) {
                return 1;
            }

            $a1 = $a;
            $e = 0;

            while (bcmod($a1, 2) == 0) {
                $a1 = bcdiv($a1, 2);
                $e = bcadd($e, 1);
            }

            if (bcmod($e, 2) == 0 || bcmod($n, 8) == 1 || bcmod($n, 8) == 7) {
                $s = 1;
            } else {
                $s = - 1;
            }

            if ($a1 == 1) {
                return $s;
            }

            if (bcmod($n, 4) == 3 && bcmod($a1, 4) == 3) {
                $s = - $s;
            }

            return bcmul($s, $this->jacobi(bcmod($n, $a1), $a1));
        }
    }

    public function intToString($x)
    {
        $math = $this;

        if ($math->cmp($x, 0) == 0) {
            return chr(0);
        }

        if ($math->cmp($x, 0) > 0) {
            $result = "";

            while ($math->cmp($x, 0) > 0) {
                $q = $math->div($x, 256, 0);
                $r = $math->mod($x, 256);
                $ascii = chr($r);

                $result = $ascii . $result;
                $x = $q;
            }

            return $result;
        }
    }

    public function stringToInt($s)
    {
        $math = $this;
        $result = 0;

        for ($c = 0; $c < strlen($s); $c ++) {
            $result = $math->add($math->mul(256, $result), ord($s[$c]));
        }

        return $result;
    }

    public function digestInteger($m)
    {
        return $this->stringToInt(hash('sha1', $this->intToString($m), true));
    }
}
