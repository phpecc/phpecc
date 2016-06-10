<?php

namespace Mdanter\Ecc\Random;


use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Util\NumberSize;

class RandomNumberGenerator implements RandomNumberGeneratorInterface
{
    /**
     * @var GmpMathInterface
     */
    private $adapter;

    /**
     * RandomNumberGenerator constructor.
     * @param GmpMathInterface $adapter
     */
    public function __construct(GmpMathInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param \GMP $max
     * @return int
     */
    public function generate(\GMP $max)
    {
        $numBits = NumberSize::bnNumBits($this->adapter, $max);
        $numBytes = ceil($numBits / 8);

        // Generate an integer of size >= $numBits
        $bytes = random_bytes($numBytes);
        $value = gmp_init(0, 10);
        for ($i = 0; $i < $numBytes; $i++) {
            $value = gmp_or($value, gmp_mul(ord($bytes[$i]), gmp_pow(2, $i * 8)));
        }

        $mask = gmp_sub(gmp_pow(2, $numBits), 1);
        $integer = gmp_and($value, $mask);

        return gmp_strval($integer, 10);
    }
}