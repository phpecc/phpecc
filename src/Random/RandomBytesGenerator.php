<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Util\NumberSize;

class RandomBytesGenerator implements RandomNumberGeneratorInterface
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
        $random = random_bytes($bytes);
        return $this->adapter->hexDec(bin2hex($random));
    }
}
