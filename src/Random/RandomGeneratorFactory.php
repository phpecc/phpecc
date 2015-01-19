<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\PrivateKeyInterface;
use Mdanter\Ecc\RandomNumberGeneratorInterface;
use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\Math\MathAdapterFactory;

class RandomGeneratorFactory
{

    private static $adapter;
    
    public static function setMathAdapter(MathAdapterInterface $adapter)
    {
        self::$adapter = $adapter;
    }
    
    public static function getRandomGenerator($debug = false)
    {
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
            new URandomNumberGenerator(self::$adapter ?: MathAdapterFactory::getAdapter($debug)),
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

    public static function getHmacRandomGenerator($math, PrivateKeyInterface $privateKey, $algo, $messageHash, $debug = false)
    {
        return self::wrapAdapter(
            new HmacRandomNumberGenerator(self::$adapter ?: MathAdapterFactory::getAdapter($debug), $privateKey, $messageHash, $algo),
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
