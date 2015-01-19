<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\RandomNumberGeneratorInterface;
use Mdanter\Ecc\Random\RandomGeneratorFactory;

class MathAdapterFactory
{
    private static $forcedAdapter = null;
    
    public static function forceAdapter(MathAdapterInterface $adapter)
    {
        self::$forcedAdapter = $adapter;
    }
    
    public static function getAdapter($debug = false)
    {
        if (self::$forcedAdapter !== null) {
            return self::$forcedAdapter;
        }
        
        $adapter = null;
        $adapterClass = self::getAdapterClass();
        
        $adapter = new $adapterClass();
        
        return self::wrapAdapter($adapter, (bool)$debug);
    }
    
    public static function getBcMathAdapter($debug = false)
    {
        if (self::canLoad('bcmath')) {
            return self::wrapAdapter(new BcMath(), $debug);
        }
    
        throw new \RuntimeException('Please install either GMP extension.');
    }
    
    public static function getGmpAdapter($debug = false)
    {
        if (self::canLoad('gmp')) {
            return self::wrapAdapter(new Gmp(), $debug);
        }
        
        throw new \RuntimeException('Please install either GMP extension.');
    }
    
    private static function getAdapterClass($extension = null)
    {
        if (self::canLoad('gmp')) {
            return '\Mdanter\Ecc\Math\Gmp';
        }
        
        if (self::canLoad('bcmath')) {
            return '\Mdanter\Ecc\Math\BcMath';
        }
        
        throw new \RuntimeException('Please install either GMP or BCMath extensions.');
    }
    
    private static function canLoad($target)
    {
        return extension_loaded($target);
    }
    
    private static function wrapAdapter(MathAdapterInterface $adapter, $debug)
    {
        if ($debug === true) {
            return new DebugDecorator($adapter);
        }
    
        return $adapter;
    }
}
