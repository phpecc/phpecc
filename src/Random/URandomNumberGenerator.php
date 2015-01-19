<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\RandomNumberGeneratorInterface;
use Mdanter\Ecc\Util\NumberSize;

class URandomNumberGenerator implements RandomNumberGeneratorInterface
{
    
    private $adapter;
    
    public function __construct(MathAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
    
    public function generate($max)
    {
        $bytes = NumberSize::getFlooredByteSize($this->adapter, $max);
        $iv = mcrypt_create_iv($bytes, \MCRYPT_DEV_URANDOM);
        
        return $this->adapter->hexDec(bin2hex($iv));
    }
}
