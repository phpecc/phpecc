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
    
    private function getByteSize($number)
    {
        // Shameless rip of https://github.com/ircmaxell/RandomLib/blob/master/lib/RandomLib/Generator.php#L307-L311
        $log2 = 0;
        
        while ($number = $this->adapter->rightShift($number, 1)) {
            $log2++;
        }
        
        return floor($log2 / 8);
    }
}
