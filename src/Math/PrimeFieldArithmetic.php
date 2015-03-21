<?php

namespace Mdanter\Ecc\Math;

class PrimeFieldArithmetic
{
    /**
     * @var MathAdapterInterface
     */
    private $adapter;

    /**
     * @var
     */
    private $prime;

    /**
     * @param MathAdapterInterface $adapter
     * @param $prime
     */
    public function __construct(MathAdapterInterface $adapter, $prime)
    {
        $this->adapter = $adapter;
        $this->prime = $prime;
    }

    /**
     * @param $augend
     * @param $addend
     * @return int|string
     */
    public function add($augend, $addend)
    {
        return $this->adapter->mod($this->adapter->add($augend, $addend), $this->prime);
    }

    /**
     * @param $minuend
     * @param $subtrahend
     * @return int|string
     */
    public function sub($minuend, $subtrahend)
    {
        return $this->adapter->mod($this->adapter->sub($minuend, $subtrahend), $this->prime);
    }

    /**
     * @param $multiplier
     * @param $muliplicand
     * @return int|string
     */
    public function mul($multiplier, $muliplicand)
    {
        return $this->adapter->mod($this->adapter->mul($multiplier, $muliplicand), $this->prime);
    }

    /**
     * @param $dividend
     * @param $divisor
     * @return int|string
     */
    public function div($dividend, $divisor)
    {
        return $this->adapter->mod($this->adapter->mul($dividend, $this->adapter->inverseMod($divisor, $this->prime)), $this->prime);
    }

    /**
     * @param $base
     * @param $exponent
     * @return mixed
     */
    public function pow($base, $exponent)
    {
        return $this->adapter->powmod($base, $exponent, $this->prime);
    }
}
