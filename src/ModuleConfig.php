<?php
namespace Mdanter\Ecc;

use Mdanter\Ecc\Theory\Bc;
use Mdanter\Ecc\Theory\Gmp;

/**
 * Temporary class to handle GMP/BCMath loading while reefactorisation is under way.
 *
 * @author thibaud
 *
 */
final class ModuleConfig
{

    private static $useGmp = false;

    private static $useBcMath = true;

    private static $hasGmpExt = null;

    private static $hasBcMathExt = null;

    public static function useGmp()
    {
        if (!self::hasGmpExt()) {
            throw new \Exception("the GMP php extension is required.");
        }

        self::$useGmp = true;
        self::$useBcMath = false;

        NumberTheory::setTheoryAdapter(new Gmp(NumberTheory::$smallprimes));
    }

    public static function useBcMath()
    {
        if (!self::hasBcMathExt()) {
            throw new \Exception("the BcMath php extension is required.");
        }

        self::$useGmp = false;
        self::$useBcMath = true;

        NumberTheory::setTheoryAdapter(new Bc(NumberTheory::$smallprimes));
    }

    public static function hasGmp()
    {
        return self::$useGmp && self::hasGmpExt();
    }

    public static function hasBcMath()
    {
        return self::$useBcMath && self::hasBcMathExt();
    }

    protected static function hasGmpExt()
    {
        if (is_null(self::$hasGmpExt)) {
            self::$hasGmpExt = extension_loaded('gmp');
        }

        return self::$hasGmpExt;
    }

    protected static function hasBcMathExt()
    {
        if (is_null(self::$hasBcMathExt)) {
            self::$hasBcMathExt = extension_loaded('bcmath');
        }

        return self::$hasBcMathExt;
    }
}
