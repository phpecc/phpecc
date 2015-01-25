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
        return gmp_cmp(gmp_init($first, 10), gmp_init($other, 10));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::mod()
     */
    public function mod($number, $modulus)
    {
        return gmp_strval(gmp_mod(gmp_init($number, 10), gmp_init($modulus, 10)));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::add()
     */
    public function add($augend, $addend)
    {
        return gmp_strval(gmp_add(gmp_init($augend, 10), gmp_init($addend, 10)));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::sub()
     */
    public function sub($minuend, $subtrahend)
    {
        return gmp_strval(gmp_sub(gmp_init($minuend, 10), gmp_init($subtrahend, 10)));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::mul()
     */
    public function mul($multiplier, $multiplicand)
    {
        return gmp_strval(gmp_mul(gmp_init($multiplier, 10), gmp_init($multiplicand, 10)));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::div()
     */
    public function div($dividend, $divisor)
    {
        return gmp_strval(gmp_div(gmp_init($dividend, 10), gmp_init($divisor, 10)));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::pow()
     */
    public function pow($base, $exponent)
    {
        return gmp_strval(gmp_pow(gmp_init($base, 10), $exponent));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::bitwiseAnd()
     */
    public function bitwiseAnd($first, $other)
    {
        return gmp_strval(gmp_and(gmp_init($first, 10), gmp_init($other, 10)));
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
     * @see \Mdanter\Ecc\MathAdapter::bitwiseXor()
     */
    public function bitwiseXor($first, $other)
    {
        return gmp_strval(gmp_xor(gmp_init($first, 10), gmp_init($other, 10)));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapter::leftShift()
     */
    public function leftShift($number, $positions)
    {
        // Shift 1 left = mul by 2
        return gmp_strval(gmp_mul(gmp_init($number), gmp_pow(2, $positions)));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapter::toString()
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

        return gmp_strval(gmp_powm(gmp_init($base, 10), gmp_init($exponent, 10), gmp_init($modulus, 10)));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::isPrime()
     */
    public function isPrime($n)
    {
        $prob = gmp_prob_prime(gmp_init($n, 10));

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
        return gmp_strval(gmp_nextprime(gmp_init($starting_value, 10)));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::inverseMod()
     */
    public function inverseMod($a, $m)
    {
        return gmp_strval(gmp_invert(gmp_init($a, 10), gmp_init($m, 10)));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::jacobi()
     */
    public function jacobi($a, $n)
    {
        return gmp_strval(gmp_jacobi(gmp_init($a, 10), gmp_init($n, 10)));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::intToString()
     */
    public function intToString($x)
    {
        $x = gmp_init($x, 10);

        if (gmp_cmp($x, 0) == 0) {
            return chr(0);
        }

        if (gmp_cmp($x, 0) > 0) {
            $result = "";

            while (gmp_cmp($x, 0) > 0) {
                $q = gmp_div($x, 256, 0);
                $r = gmp_mod($x, 256);
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
        $sLen = strlen($s);

        for ($c = 0; $c < $sLen; $c ++) {
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

    public function baseConvert($number, $from, $to)
    {
        return gmp_strval(gmp_init($number, $from), $to);
    }
}
