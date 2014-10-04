<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\MathAdapter;

class Gmp implements MathAdapter
{
    function cmp($first, $other)
    {
        return gmp_cmp($first, $other);
    }

    function mod($number, $modulus)
    {
        $res = gmp_div_r($number, $modulus);

        if (gmp_cmp(0, $res) > 0) {
            $res = gmp_add($modulus, $res);
        }

        return gmp_strval($res);
    }

    function add($augend, $addend)
    {
        return gmp_add($augend, $addend);
    }

    function sub($minuend, $subtrahend)
    {
        return gmp_sub($minuend, $subtrahend);
    }

    function mul($multiplier, $multiplicand)
    {
        return gmp_mul($multiplier, $multiplicand);
    }

    function div($dividend, $divisor)
    {
        return gmp_div($dividend, $divisor);
    }

    function pow($base, $exponent)
    {
        return gmp_pow($base, $exponent);
    }

    function rand($n)
    {
        $random = gmp_strval(gmp_random());
        $small_rand = rand();

        while (gmp_cmp($random, $n) > 0) {
            $random = gmp_div($random, $small_rand, GMP_ROUND_ZERO);
        }

        return gmp_strval($random);
    }

    function bitwiseAnd($first, $other)
    {
        return gmp_and($first, $other);
    }

    function toString($value)
    {
        return gmp_strval($value);
    }

    public function hexDec($hex)
    {
        return gmp_strval(gmp_init($hex, 16), 10);
    }

    public function decHex($dec)
    {
        return gmp_strval(gmp_init($dec, 10), 16);
    }

    public function powmod($base, $exponent, $modulus)
    {
        if ($exponent < 0) {
            throw new \InvalidArgumentException("Negative exponents ($exponent) not allowed.");
        }

        return gmp_strval(gmp_powm($base, $exponent, $modulus));
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
        return gmp_strval(gmp_nextprime($starting_value));
    }

    public function inverseMod($a, $m)
    {
        return gmp_strval(gmp_invert($a, $m));
    }

    public function jacobi($a, $n)
    {
        return gmp_strval(gmp_jacobi($a, $n));
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
