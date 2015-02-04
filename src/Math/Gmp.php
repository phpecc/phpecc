<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\MathAdapterInterface;

class Gmp implements MathAdapterInterface
{
    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::cmp()
     */
    public function cmp($first, $other)
    {
        return gmp_cmp($first, $other);
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::mod()
     */
    public function mod($number, $modulus)
    {
        return gmp_strval(gmp_mod($number, $modulus));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::add()
     */
    public function add($augend, $addend)
    {
        return gmp_strval(gmp_add($augend, $addend));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::sub()
     */
    public function sub($minuend, $subtrahend)
    {
        return gmp_strval(gmp_sub($minuend, $subtrahend));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::mul()
     */
    public function mul($multiplier, $multiplicand)
    {
        return gmp_strval(gmp_mul($multiplier, $multiplicand));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::div()
     */
    public function div($dividend, $divisor)
    {
        return gmp_strval(gmp_div($dividend, $divisor));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::pow()
     */
    public function pow($base, $exponent)
    {
        return gmp_strval(gmp_pow($base, $exponent));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::bitwiseAnd()
     */
    public function bitwiseAnd($first, $other)
    {
        return gmp_strval(gmp_and($first, $other));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapter::rightShift()
     */
    public function rightShift($number, $positions)
    {
        // Shift 1 right = div / 2
        return gmp_strval(gmp_div($number, gmp_pow(2, $positions)));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapter::rightShift()
     */
    public function leftShift($number, $positions)
    {
        // Shift 1 right = mul * 2
        return gmp_strval(gmp_mul($number, gmp_pow(2, $positions)));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::toString()
     */
    public function toString($value)
    {
        if (is_resource($value)) {
            return gmp_strval($value);
        }

        return $value;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::hexDec()
     */
    public function hexDec($hex)
    {
        return gmp_strval(gmp_init($hex, 16), 10);
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::decHex()
     */
    public function decHex($dec)
    {
        $hex = gmp_strval(gmp_init($dec, 10), 16);

        if (strlen($hex) % 2 != 0) {
            $hex = '0'.$hex;
        }

        return $hex;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::powmod()
     */
    public function powmod($base, $exponent, $modulus)
    {
        if ($exponent < 0) {
            throw new \InvalidArgumentException("Negative exponents ($exponent) not allowed.");
        }

        return gmp_strval(gmp_powm($base, $exponent, $modulus));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::isPrime()
     */
    public function isPrime($n)
    {
        $prob = gmp_prob_prime($n);

        if ($prob > 0) {
            return true;
        }

        return false;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::nextPrime()
     */
    public function nextPrime($starting_value)
    {
        return gmp_strval(gmp_nextprime($starting_value));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::inverseMod()
     */
    public function inverseMod($a, $m)
    {
        return gmp_strval(gmp_invert($a, $m));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::jacobi()
     */
    public function jacobi($a, $n)
    {
        return gmp_strval(gmp_jacobi($a, $n));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::intToString()
     */
    public function intToString($x)
    {
        $math = $this;

        if (gmp_cmp($x, 0) == 0) {
            return chr(0);
        }

        if ($math->cmp($x, 0) > 0) {
            $result = "";

            while (gmp_cmp($x, 0) > 0) {
                $q = gmp_div($x, 256, 0);
                $r = $math->mod($x, 256);
                $ascii = chr($r);

                $result = $ascii.$result;
                $x = $q;
            }

            return $result;
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::stringToInt()
     */
    public function stringToInt($s)
    {
        $math = $this;
        $result = 0;

        for ($c = 0; $c < strlen($s); $c ++) {
            $result = $math->add($math->mul(256, $result), ord($s[$c]));
        }

        return $result;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::digestInteger()
     */
    public function digestInteger($m)
    {
        return $this->stringToInt(hash('sha1', $this->intToString($m), true));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::gcd2()
     */
    public function gcd2($a, $b)
    {
        while ($a) {
            $temp = $a;
            $a = $this->mod($b, $a);
            $b = $temp;
        }

        return gmp_strval($b);
    }
}
