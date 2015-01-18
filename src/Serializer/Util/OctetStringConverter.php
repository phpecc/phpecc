<?php

namespace Mdanter\Ecc\Serializer\Util;

use Mdanter\Ecc\MathAdapterInterface;
/**
 *
 * @link https://tools.ietf.org/html/rfc3447#page-9
 * @author thibaud
 *        
 */
class OctetStringConverter
{

    public static function i2osp(MathAdapterInterface $adapter, $x, $xLen)
    {
    	if ($adapter->cmp($x, $adapter->pow(256, $xLen)) >= 0) {
    	    throw new \RuntimeException('Integer too large.');
    	}
    	
    	$s = '';
    	
    	while ($adapter->cmp($x, 0) > 0) {
    	    $s = $adapter->bitwiseAnd($x, 255) . $s;
    	    $x = $adapter->rightShift($x, 8);    
    	}
    	
    	return pack('H*', $s);
    }
    
    public static function os2ip(MathAdapterInterface $adapter, $x)
    {
        
    }
}