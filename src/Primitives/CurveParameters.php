<?php

namespace Mdanter\Ecc\Primitives;

use Mdanter\Ecc\Math\GmpMath;

class CurveParameters
{
    /**
     * Elliptic curve over the field of integers modulo a prime.
     *
     * @var resource|\GMP
     */
    protected $a;

    /**
     *
     * @var resource|\GMP
     */
    protected $b;

    /**
     *
     * @var resource|\GMP
     */
    protected $prime;

    /**
     * Binary length of keys associated with these curve parameters
     *
     * @var int
     */
    protected $size;

    /**
     * @param int $size
     * @param resource|\GMP $prime
     * @param resource|\GMP $a
     * @param resource|\GMP $b
     */
    public function __construct($size, $prime, $a, $b)
    {
        if (!GmpMath::checkGmpValue($prime)) {
            throw new \InvalidArgumentException('Invalid argument #2 to CurveParameters constructor - must pass GMP resource or \GMP instance');
        }

        if (!GmpMath::checkGmpValue($a)) {
            throw new \InvalidArgumentException('Invalid argument #3 to CurveParameters constructor - must pass GMP resource or \GMP instance');
        }

        if (!GmpMath::checkGmpValue($b)) {
            throw new \InvalidArgumentException('Invalid argument #4 to CurveParameters constructor - must pass GMP resource or \GMP instance');
        }
        
        $this->size = $size;
        $this->prime = $prime;
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * @return resource|\GMP
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * @return resource|\GMP
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * @return resource|\GMP
     */
    public function getPrime()
    {
        return $this->prime;
    }

    /**
     * @return resource|int
     */
    public function getSize()
    {
        return $this->size;
    }
}
