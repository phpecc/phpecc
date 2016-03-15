<?php

namespace Mdanter\Ecc\Primitives;

class CurveParameters
{
    /**
     * Elliptic curve over the field of integers modulo a prime.
     *
     * @var int|string
     */
    protected $a = 0;

    /**
     *
     * @var int|string
     */
    protected $b = 0;

    /**
     *
     * @var int|string
     */
    protected $prime = 0;

    /**
     * Binary length of keys associated with these curve parameters
     *
     * @var int
     */
    protected $size = 0;

    /**
     * @param int $size
     * @param int|string $prime
     * @param int|string $a
     * @param int|string $b
     */
    public function __construct($size, $prime, $a, $b)
    {
        $this->size = $size;
        $this->prime = $prime;
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * @return int|string
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * @return int|string
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * @return int|string
     */
    public function getPrime()
    {
        return $this->prime;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }
}
