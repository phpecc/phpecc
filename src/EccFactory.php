<?php

namespace Mdanter\Ecc;

use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Math\BcMath;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Curves\SecgCurve;

class EccFactory
{

    private static function getAdapter()
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
     *
     * @param MathAdapter $adapter
     * @return \Mdanter\Ecc\Curves\NistCurve
     */
    public static function getNistCurves(MathAdapter $adapter = null)
    {
        return new NistCurve($adapter ?: self::getAdapter());
    }

    /**
     *
     * @param MathAdapter $adapter
     * @return \Mdanter\Ecc\Curves\SecgCurve
     */
    public static function getSecgCurves(MathAdapter $adapter = null)
    {
        return new SecgCurve($adapter ?: self::getAdapter());
    }

    /**
     *
     * @param number|string $prime
     * @param number|string $a
     * @param number|string $b
     * @param MathAdapter $adapter
     * @return \Mdanter\Ecc\CurveFpInterface
     */
    public static function createCurve($prime, $a, $b, MathAdapter $adapter = null)
    {
        return new CurveFp($prime, $a, $b, $adapter ?: self::getAdapter());
    }
}
