<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Util\NumberSize;

class URandomNumberGenerator implements RandomNumberGeneratorInterface
{
    /**
     * @var MathAdapterInterface
     */
    private $adapter;

    /**
     * @param MathAdapterInterface $adapter
     */
    public function __construct(MathAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param int|string $max
     * @return int|string
     */
    public function generate($max)
    {
        $bytes = NumberSize::getFlooredByteSize($this->adapter, $max);
        $iv = mcrypt_create_iv($bytes, \MCRYPT_DEV_URANDOM);

        return $this->adapter->hexDec(bin2hex($iv));
    }
}
