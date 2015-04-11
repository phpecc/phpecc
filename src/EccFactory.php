<?php

namespace Mdanter\Ecc;

use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Curves\SecgCurve;
use Mdanter\Ecc\Math\MathAdapterFactory;
use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Primitives\CurveFp;

/**
 * Static factory class providing factory methods to work with NIST and SECG recommended curves.
 */
class EccFactory
{
    /**
     * Selects and creates the most appropriate adapter for the running environment.
     *
     * @param $debug [optional] Set to true to get a trace of all mathematical operations
     *
     * @throws \RuntimeException
     * @return MathAdapterInterface
     */
    public static function getAdapter($debug = false)
    {
        return MathAdapterFactory::getAdapter($debug);
    }

    /**
     * Returns a number theory library initialized with the respective math adaptor.
     * Contains useful modular/polynomial functions
     *
     * @param  MathAdapterInterface $adapter [optional] Defaults to the return value EccFactory::getAdapter().
     * @return \Mdanter\Ecc\Math\NumberTheory
     */
    public static function getNumberTheory(MathAdapterInterface $adapter = null)
    {
        $adapter = $adapter ?: self::getAdapter();
        return $adapter->getNumberTheory();
    }

    /**
     * Returns a factory to create NIST Recommended curves and generators.
     *
     * @param  MathAdapterInterface $adapter [optional] Defaults to the return value of EccFactory::getAdapter().
     * @return NistCurve
     */
    public static function getNistCurves(MathAdapterInterface $adapter = null)
    {
        return new NistCurve($adapter ?: self::getAdapter());
    }

    /**
     * Returns a factory to return SECG Recommended curves and generators.
     *
     * @param  MathAdapterInterface $adapter [optional] Defaults to the return value of EccFactory::getAdapter().
     * @return SecgCurve
     */
    public static function getSecgCurves(MathAdapterInterface $adapter = null)
    {
        return new SecgCurve($adapter ?: self::getAdapter());
    }

    /**
     * Creates a new curve from arbitrary parameters.
     *
     * @param  int|string           $prime
     * @param  int|string           $a
     * @param  int|string           $b
     * @param  MathAdapterInterface $adapter [optional] Defaults to the return value of EccFactory::getAdapter().
     * @return \Mdanter\Ecc\Primitives\CurveFpInterface
     */
    public static function createCurve($prime, $a, $b, MathAdapterInterface $adapter = null)
    {
        return new CurveFp($prime, $a, $b, $adapter ?: self::getAdapter());
    }

    /**
     *
     * @param  MathAdapterInterface $adapter [optional] Defaults to the return value of EccFactory::getAdapteR()
     * @return \Mdanter\Ecc\Crypto\Signature\Signer;

     */
    public static function getSigner(MathAdapterInterface $adapter = null)
    {
        return new Signer($adapter ?: self::getAdapter());
    }
}
