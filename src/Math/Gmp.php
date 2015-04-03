<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\Primitives\CurveFpInterface;
use Mdanter\Ecc\Primitives\GeneratorPoint;

class Gmp implements MathAdapterInterface
{
    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::cmp()
     */
    public function cmp($first, $other)
    {
        return gmp_cmp(gmp_init($first, 10), gmp_init($other, 10));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::mod()
     */
    public function mod($number, $modulus)
    {
        return gmp_strval(gmp_mod(gmp_init($number, 10), gmp_init($modulus, 10)));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::add()
     */
    public function add($augend, $addend)
    {
        return gmp_strval(gmp_add(gmp_init($augend, 10), gmp_init($addend, 10)));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::sub()
     */
    public function sub($minuend, $subtrahend)
    {
        return gmp_strval(gmp_sub(gmp_init($minuend, 10), gmp_init($subtrahend, 10)));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::mul()
     */
    public function mul($multiplier, $multiplicand)
    {
        return gmp_strval(gmp_mul(gmp_init($multiplier, 10), gmp_init($multiplicand, 10)));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::div()
     */
    public function div($dividend, $divisor)
    {
        return gmp_strval(gmp_div(gmp_init($dividend, 10), gmp_init($divisor, 10)));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::pow()
     */
    public function pow($base, $exponent)
    {
        return gmp_strval(gmp_pow(gmp_init($base, 10), $exponent));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::bitwiseAnd()
     */
    public function bitwiseAnd($first, $other)
    {
        return gmp_strval(gmp_and(gmp_init($first, 10), gmp_init($other, 10)));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapter::rightShift()
     */
    public function rightShift($number, $positions)
    {
        // Shift 1 right = div / 2
        return gmp_strval(gmp_div($number, gmp_pow(2, $positions)));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapter::bitwiseXor()
     */
    public function bitwiseXor($first, $other)
    {
        return gmp_strval(gmp_xor(gmp_init($first, 10), gmp_init($other, 10)));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapter::leftShift()
     */
    public function leftShift($number, $positions)
    {
        // Shift 1 left = mul by 2
        return gmp_strval(gmp_mul(gmp_init($number), gmp_pow(2, $positions)));
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::hexDec()
     */
    public function hexDec($hex)
    {
        return gmp_strval(gmp_init($hex, 16), 10);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::nextPrime()
     */
    public function nextPrime($starting_value)
    {
        return gmp_strval(gmp_nextprime(gmp_init($starting_value, 10)));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::inverseMod()
     */
    public function inverseMod($a, $m)
    {
        return gmp_strval(gmp_invert(gmp_init($a, 10), gmp_init($m, 10)));
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::jacobi()
     */
    public function jacobi($a, $n)
    {
        return gmp_strval(gmp_jacobi(gmp_init($a, 10), gmp_init($n, 10)));
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::digestInteger()
     */
    public function digestInteger($m)
    {
        return $this->stringToInt(hash('sha1', $this->intToString($m), true));
    }

    /**
     * {@inheritDoc}
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

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::baseConvert()
     */
    public function baseConvert($number, $from, $to)
    {
        return gmp_strval(gmp_init($number, $from), $to);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::getNumberTheory()
     */
    public function getNumberTheory()
    {
        return new NumberTheory($this);
    }

    /**
     * @param $modulus
     * @return ModularArithmetic
     */
    public function getModularArithmetic($modulus)
    {
        return new ModularArithmetic($this, $modulus);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::getPrimeFieldArithmetic()
     */
    public function getPrimeFieldArithmetic(CurveFpInterface $curve)
    {
        return $this->getModularArithmetic($curve->getPrime());
    }

    /**
     * @param GeneratorPoint $generatorPoint
     * @param $input
     * @return EcMath
     */
    public function getEcMath(GeneratorPoint $generatorPoint, $input)
    {
        return new EcMath($input, $generatorPoint, $this);
    }
}
