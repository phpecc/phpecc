<?php

namespace Mdanter\Ecc\Random;


use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Util\NumberSize;

class RandomNumberGenerator implements RandomNumberGeneratorInterface
{
    /**
     * @var MathAdapterInterface
     */
    private $adapter;

    /**
     * RandomNumberGenerator constructor.
     * @param MathAdapterInterface $adapter
     */
    public function __construct(MathAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param int|string $max
     * @return int
     */
    public function generate($max)
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