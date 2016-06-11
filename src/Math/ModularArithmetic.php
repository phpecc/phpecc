<?php

namespace Mdanter\Ecc\Math;

class ModularArithmetic
{
    /**
     * @var GmpMathInterface
     */
    private $adapter;

    /**
     * @var resource|\GMP
     */
    private $modulus;

    /**
     * @param GmpMathInterface $adapter
     * @param resource|\GMP $modulus
     */
    public function __construct(GmpMathInterface $adapter, $modulus)
    {
        if (!GmpMath::checkGmpValue($modulus)) {
            throw new \InvalidArgumentException('Invalid argument #2 to ModularArithmetic constructor - must pass GMP resource or \GMP instance');
        }

        $this->adapter = $adapter;
        $this->modulus = $modulus;
    }

    /**
     * @param resource|\GMP $augend
     * @param resource|\GMP $addend
     * @return \GMP
     */
    public function add($augend, $addend)
    {
        return $this->adapter->mod($this->adapter->add($augend, $addend), $this->modulus);
    }

    /**
     * @param resource|\GMP $minuend
     * @param resource|\GMP $subtrahend
     * @return \GMP
     */
    public function sub($minuend, $subtrahend)
    {
        return $this->adapter->mod($this->adapter->sub($minuend, $subtrahend), $this->modulus);
    }

    /**
     * @param resource|\GMP $multiplier
     * @param resource|\GMP $muliplicand
     * @return \GMP
     */
    public function mul($multiplier, $muliplicand)
    {
        return $this->adapter->mod($this->adapter->mul($multiplier, $muliplicand), $this->modulus);
    }

    /**
     * @param resource|\GMP $dividend
     * @param resource|\GMP $divisor
     * @return \GMP
     */
    public function div($dividend, $divisor)
    {
        return $this->adapter->mod($this->adapter->mul($dividend, $this->adapter->inverseMod($divisor, $this->modulus)), $this->modulus);
    }

    /**
     * @param resource|\GMP $base
     * @param resource|\GMP $exponent
     * @return \GMP
     */
    public function pow($base, $exponent)
    {
        return $this->adapter->powmod($base, $exponent, $this->modulus);
    }
}
