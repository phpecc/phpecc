<?php

namespace Mdanter\Ecc\Math;

class GmpMath implements GmpMathInterface
{
    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::cmp()
     */
    public function cmp(\GMP $first, \GMP $other)
    {
        return gmp_cmp($first, $other);
    }

    /**
     * @param \GMP $first
     * @param \GMP $other
     * @return bool
     */
    public function equals(\GMP $first, \GMP $other)
    {
        return gmp_cmp($first, $other) === 0;
    }
    
    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::mod()
     */
    public function mod(\GMP $number, \GMP $modulus)
    {
        return gmp_mod($number, $modulus);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::add()
     */
    public function add(\GMP $augend, \GMP $addend)
    {
        return gmp_add($augend, $addend);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::sub()
     */
    public function sub(\GMP $minuend, \GMP $subtrahend)
    {
        return gmp_sub($minuend, $subtrahend);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::mul()
     */
    public function mul(\GMP $multiplier, \GMP $multiplicand)
    {
        return gmp_mul($multiplier, $multiplicand);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::div()
     */
    public function div(\GMP $dividend, \GMP $divisor)
    {
        return gmp_div($dividend, $divisor);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::pow()
     */
    public function pow(\GMP $base, $exponent)
    {
        return gmp_pow($base, $exponent);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::bitwiseAnd()
     */
    public function bitwiseAnd(\GMP $first, \GMP $other)
    {
        return gmp_and($first, $other);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::rightShift()
     */
    public function rightShift(\GMP $number, $positions)
    {
        // Shift 1 right = div / 2
        return gmp_div($number, gmp_pow(gmp_init(2, 10), $positions));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::bitwiseXor()
     */
    public function bitwiseXor(\GMP $first, \GMP $other)
    {
        return gmp_xor($first, $other);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::leftShift()
     */
    public function leftShift(\GMP $number, $positions)
    {
        // Shift 1 left = mul by 2
        return gmp_mul($number, gmp_pow(2, $positions));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::toString()
     */
    public function toString(\GMP $value)
    {
        return gmp_strval($value);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::hexDec()
     */
    public function hexDec($hex)
    {
        return gmp_strval(gmp_init($hex, 16), 10);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::decHex()
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
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::powmod()
     */
    public function powmod(\GMP $base, \GMP $exponent, \GMP $modulus)
    {
        if ($this->cmp($exponent, gmp_init(0, 10)) < 0) {
            throw new \InvalidArgumentException("Negative exponents ($exponent) not allowed.");
        }

        return gmp_powm($base, $exponent, $modulus);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::isPrime()
     */
    public function isPrime(\GMP $n)
    {
        $prob = gmp_prob_prime($n);

        if ($prob > 0) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::nextPrime()
     */
    public function nextPrime(\GMP $starting_value)
    {
        return gmp_nextprime($starting_value);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::inverseMod()
     */
    public function inverseMod(\GMP $a, \GMP $m)
    {
        return gmp_invert($a, $m);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::jacobi()
     */
    public function jacobi(\GMP $a, \GMP $n)
    {
        return gmp_jacobi($a, $n);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::intToString()
     */
    public function intToString(\GMP $x)
    {
        if (gmp_cmp($x, 0) == 0) {
            return chr(0);
        }

        if (gmp_cmp($x, 0) > 0) {
            $result = "";

            while (gmp_cmp($x, 0) > 0) {
                $q = gmp_div($x, 256, 0);
                $r = gmp_mod($x, 256);

                $ascii = chr(gmp_strval($r));

                $result = $ascii . $result;
                $x = $q;
            }

            return $result;
        }

        throw new \RuntimeException('Unable to convert int to string');
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::stringToInt()
     */
    public function stringToInt($s)
    {
        $result = gmp_init(0, 10);
        $sLen = strlen($s);

        for ($c = 0; $c < $sLen; $c ++) {
            $result = gmp_add(gmp_mul(256, $result), gmp_init(ord($s[$c]), 10));
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::digestInteger()
     */
    public function digestInteger(\GMP $m)
    {
        return $this->stringToInt(hash('sha1', $this->intToString($m), true));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::gcd2()
     */
    public function gcd2(\GMP $a, \GMP $b)
    {
        while ($this->cmp($a, gmp_init(0)) > 0) {
            $temp = $a;
            $a = $this->mod($b, $a);
            $b = $temp;
        }

        return $b;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::baseConvert()
     */
    public function baseConvert($number, $from, $to)
    {
        return gmp_strval(gmp_init($number, $from), $to);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::getNumberTheory()
     */
    public function getNumberTheory()
    {
        return new NumberTheory($this);
    }

    /**
     * @param \GMP $modulus
     * @return ModularArithmetic
     */
    public function getModularArithmetic(\GMP $modulus)
    {
        return new ModularArithmetic($this, $modulus);
    }
}
