<?php

namespace Mdanter\Ecc;

use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Math\BcMath;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Curves\SecgCurve;
use Mdanter\Ecc\NumberTheory;

/**
 * Static factory class providing factory methods to work with NIST and SECG recommended curves.
 *
 * @author thibaud
 *
 */
class EccFactory
{
    /**
     * Selects and creates the most appropriate adapter for the running environment.
     *
     * @throws \RuntimeException
     * @return \Mdanter\Ecc\MathAdapter
     */
    public static function getAdapter()
    {
        if (extension_loaded('gmp')) {
            return new Gmp();
        }

        if (extension_loaded('bcmath')) {
            return new BcMath();
        }

        throw new \RuntimeException('Please install either GMP or BCMath extensions.');
    }

    /**
     * Returns a number theory library initialized with the respective math adaptor.
     * Contains useful modular/polynomial functions
     *
     * @param MathAdapter $adapter [optional] Defaults to the return value EccFactory::getAdapter().
     * @return \Mdanter\Ecc\NumberTheory
     */
    public static function getNumberTheory(MathAdapter $adapter = null)
    {
        return new NumberTheory($adapter ?: self::getAdapter());
    }

    /**
     * Returns a factory to create NIST Recommended curves and generators.
     *
     * @param MathAdapter $adapter [optional] Defaults to the return value of EccFactory::getAdapter().
     * @return \Mdanter\Ecc\Curves\NistCurve
     */
    public static function getNistCurves(MathAdapter $adapter = null)
    {
        return new NistCurve($adapter ?: self::getAdapter());
    }

    /**
     * Returns a factory to return SECG Recommended curves and generators.
     *
     * @param MathAdapter $adapter [optional] Defaults to the return value of EccFactory::getAdapter().
     * @return \Mdanter\Ecc\Curves\SecgCurve
     */
    public static function getSecgCurves(MathAdapter $adapter = null)
    {
        return new SecgCurve($adapter ?: self::getAdapter());
    }

    /**
     * Creates a new curve from arbitrary parameters.
     *
     * @param int|string $prime
     * @param int|string $a
     * @param int|string $b
     * @param MathAdapter $adapter [optional] Defaults to the return value of EccFactory::getAdapter().
     * @return \Mdanter\Ecc\CurveFpInterface
     */
    public static function createCurve($prime, $a, $b, MathAdapter $adapter = null)
    {
        return new CurveFp($prime, $a, $b, $adapter ?: self::getAdapter());
    }
}
