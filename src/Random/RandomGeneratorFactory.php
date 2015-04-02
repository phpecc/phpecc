<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;

use Mdanter\Ecc\Math\MathAdapterFactory;

class RandomGeneratorFactory
{
    /**
     * @var RandomNumberGeneratorInterface|null
     */
    private static $forcedGenerator = null;

    /**
     * @param RandomNumberGeneratorInterface $generator
     */
    public static function forceGenerator(RandomNumberGeneratorInterface $generator = null)
    {
        self::$forcedGenerator = $generator;
    }

    /**
     * @param bool $debug
     * @return DebugDecorator|RandomNumberGeneratorInterface|null
     */
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

        throw new \RuntimeException('No usable RandomGenerator was found');
    }

    /**
     * @param bool $debug
     * @return DebugDecorator|RandomNumberGeneratorInterface
     */
    public static function getUrandomGenerator($debug = false)
    {
        return self::wrapAdapter(
            new URandomNumberGenerator(MathAdapterFactory::getAdapter($debug)),
            'urandom',
            $debug
        );
    }

    /**
     * @param bool $debug
     * @param bool $noWarn
     * @return DebugDecorator|RandomNumberGeneratorInterface
     */
    public static function getGmpRandomGenerator($debug = false, $noWarn = false)
    {
        return self::wrapAdapter(
            new GmpRandomNumberGenerator($noWarn),
            'gmp',
            $debug
        );
    }

    /**
     * @param PrivateKeyInterface $privateKey
     * @param $messageHash
     * @param $algo
     * @param bool                $debug
     * @return DebugDecorator|RandomNumberGeneratorInterface
     */
    public static function getHmacRandomGenerator(PrivateKeyInterface $privateKey, $messageHash, $algo, $debug = false)
    {
        return self::wrapAdapter(
            new HmacRandomNumberGenerator(
                MathAdapterFactory::getAdapter($debug),
                $privateKey,
                $messageHash,
                $algo
            ),
            'rfc6979',
            $debug
        );
    }

    /**
     * @param RandomNumberGeneratorInterface $generator
     * @param $name
     * @param bool                           $debug
     * @return DebugDecorator|RandomNumberGeneratorInterface
     */
    private static function wrapAdapter(RandomNumberGeneratorInterface $generator, $name, $debug = false)
    {
        if ($debug === true) {
            return new DebugDecorator($generator, $name);
        }

        return $generator;
    }
}
