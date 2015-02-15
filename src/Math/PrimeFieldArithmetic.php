<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\MathAdapterInterface;

class PrimeFieldArithmetic
{
    private $adapter;

    private $prime;

    public function __construct(MathAdapterInterface $adapter, $prime)
    {
        $this->adapter = $adapter;
        $this->prime = $prime;
    }

    public function add($augend, $addend)
    {
        return $this->adapter->mod($this->adapter->add($augend, $addend), $this->prime);
    }

    public function sub($minuend, $subtrahend)
    {
        return $this->adapter->mod($this->adapter->sub($minuend, $subtrahend), $this->prime);
    }

    public function mul($multiplier, $muliplicand)
    {
        return $this->adapter->mod($this->adapter->mul($multiplier, $muliplicand), $this->prime);
    }

    public function div($dividend, $divisor)
    {
        return $this->adapter->mod($this->adapter->mul($dividend, $this->adapter->inverseMod($divisor, $this->prime)), $this->prime);
    }

    public function pow($base, $exponent)
    {
        return $this->adapter->powmod($base, $exponent, $this->prime);
    }
}
