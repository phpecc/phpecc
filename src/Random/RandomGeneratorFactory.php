<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\RandomNumberGeneratorInterface;
use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\Math\MathAdapterFactory;

class RandomGeneratorFactory
{
    
    private static $forcedGenerator = null;
    
    public static function forceGenerator(RandomNumberGeneratorInterface $generator = null)
    {
        self::$forcedGenerator = $generator;
    }
    
    public static function getRandomGenerator($debug = false)
    {
        if (self::$forcedGenerator !== null) {
            return self::$forcedGenerator;
        }
        
        if (extension_loaded('mcrypt')) {
            return self::getUrandomGenerator($debug);
        }
        
        if (extension_loaded('gmp') && ! defined('HHVM_VERSION')) {
            return self::getGmpRandomGenerator($debug);
        }
        
        if (extension_loaded('bcmath')) {
            return self::getBcMathRandomGenerator($debug);
        }
    }
    
    public static function getUrandomGenerator($debug = false)
    {
        return self::wrapAdapter(
            new URandomNumberGenerator(MathAdapterFactory::getAdapter($debug)),
            'urandom',
            $debug
        );
    }
    
    public static function getGmpRandomGenerator($debug = false, $noWarn = false)
    {
        return self::wrapAdapter(
            new GmpRandomNumberGenerator($noWarn),
            'gmp',
            $debug
        );
    }
    
    public static function getBcMathRandomGenerator($debug = false, $noWarn = false)
    {
        return self::wrapAdapter(
            new BcMathRandomNumberGenerator($noWarn),
            'bcmath',
            $debug
        );
    }
    
    private static function wrapAdapter(RandomNumberGeneratorInterface $generator, $name, $debug = false)
    {
        if ($debug === true) {
            return new DebugDecorator($generator, $name);
        }
        
        return $generator;
    }
}
