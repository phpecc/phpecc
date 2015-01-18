<?php

namespace Mdanter\Ecc\Util;

use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\Math\MathAdapterFactory;

class NumberSize
{
    
    public static function getCeiledByteSize(MathAdapterInterface $adapter, $x)
    {
        $log2 = 0;

        while ($x = $adapter->rightShift($x, 1)) {
            $log2++;
        }
        
        return ceil($log2 / 2);
    }
    
    public static function getFlooredByteSize(MathAdapterInterface $adapter, $x)
    {
        $log2 = 0;
    
        while ($x = $adapter->rightShift($x, 1)) {
            $log2++;
        }
    
        return floor($log2 / 2);
    }
}