<?php

namespace Mdanter\Ecc;

use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Math\BcMath;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Curves\SecCurve;

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

    public static function getNistCurves(MathAdapter $adapter = null)
    {
        return new NistCurve($adapter ?: self::getAdapter());
    }

    public static function getSecCurves(MathAdapter $adapter = null)
    {
        return new SecCurve($adapter ?: self::getAdapter());
    }

    public static function createCurve($prime, $a, $b, MathAdapter $adapter = null)
    {
        return new CurveFp($prime, $a, $b, $adapter ?: self::getAdapter());
    }
}
