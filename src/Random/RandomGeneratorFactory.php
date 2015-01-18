<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\RandomNumberGeneratorInterface;

class RandomGeneratorFactory
{

    public static function getRandomGenerator($debug = false)
    {
        if (extension_loaded('gmp')) {
            return self::getGmpRandomGenerator($debug);
        }
        
        if (extension_loaded('bcmath')) {
            return self::getBcMathRandomGenerator($debug);
        }
    }
    
    public static function getGmpRandomGenerator($debug = false)
    {
        return self::wrapAdapter(
            new GmpRandomNumberGenerator(),
            'gmp',
            $debug
        );
    }
    
    public static function getBcMathRandomGenerator($debug = false)
    {
        return self::wrapAdapter(
            new BcMathRandomNumberGenerator(),
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