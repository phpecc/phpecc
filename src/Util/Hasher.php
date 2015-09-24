<?php

namespace Mdanter\Ecc\Util;


use Mdanter\Ecc\Math\MathAdapterInterface;

class Hasher
{
    /**
     * @var string
     */
    private $algo;

    /**
     * @var MathAdapterInterface
     */
    private $math;

    /**
     * @param MathAdapterInterface $math
     * @param string $algo
     */
    public function __construct(MathAdapterInterface $math, $algo)
    {
        if (!in_array($algo, hash_algos())) {
            throw new \InvalidArgumentException('Hashing algorithm not known');
        }

        $this->algo = $algo;
        $this->math = $math;
    }

    /**
     * @return string
     */
    public function getAlgo()
    {
        return $this->algo;
    }

    /**
     * @param $string - a binary string to hash
     * @param bool|false $binary
     * @return string
     */
    public function hash($string, $binary = false)
    {
        return hash($this->algo, $string, $binary);
    }

    /**
     * Hash the data, returning a decimal.
     *
     * @param $string - a binary string to hash
     * @return int|string
     */
    public function hashDec($string)
    {
        $hex = $this->hash($string, false);
        return $this->math->hexDec($hex);
    }
}