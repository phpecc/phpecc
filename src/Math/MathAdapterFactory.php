<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\RandomNumberGeneratorInterface;
use Mdanter\Ecc\Random\RandomGeneratorFactory;

class MathAdapterFactory
{
    public static function getAdapter(RandomNumberGeneratorInterface $generator = null, $debug = false)
    {
        $adapter = null;
        $adapterClass = self::getAdapterClass();
        
        $adapter = new $adapterClass($generator);
        
        return self::wrapAdapter($adapter, (bool)$debug);
    }
    
    public static function getBcMathAdapter(RandomNumberGeneratorInterface $generator = null, $debug = false)
    {
        if (self::canLoad('bcmath')) {
            return self::wrapAdapter(new BcMath($generator), $debug);
        }
    
        throw new \RuntimeException('Please install either GMP extension.');
    }
    
    public static function getGmpAdapter(RandomNumberGeneratorInterface $generator = null, $debug = false)
    {
        if (self::canLoad('gmp')) {
            return self::wrapAdapter(new Gmp($generator), $debug);
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