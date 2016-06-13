<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;

use Mdanter\Ecc\Math\MathAdapterFactory;

class RandomGeneratorFactory
{
    /**
     * @param bool $debug
     * @return DebugDecorator|RandomNumberGeneratorInterface|null
     */
    public static function getRandomGenerator($debug = false)
    {
        return self::wrapAdapter(
            new RandomNumberGenerator(
                MathAdapterFactory::getAdapter($debug)
            ),
            'random_bytes',
            $debug
        );
    }

    /**
     * @param PrivateKeyInterface $privateKey
     * @param \GMP                $messageHash
     * @param string              $algo
     * @param bool                $debug
     * @return DebugDecorator|RandomNumberGeneratorInterface
     */
    public static function getHmacRandomGenerator(PrivateKeyInterface $privateKey, \GMP $messageHash, $algo, $debug = false)
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
