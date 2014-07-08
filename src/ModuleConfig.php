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

    public static function useGmp()
    {
        self::$useGmp = true;
        self::$useBcMath = false;
        
        NumberTheory::setTheoryAdapter(new Gmp(NumberTheory::$smallprimes));
    }

    public static function useBcMath()
    {
        self::$useGmp = false;
        self::$useBcMath = true;
        
        NumberTheory::setTheoryAdapter(new Bc(NumberTheory::$smallprimes));
    }

    public static function hasGmp()
    {
        return self::$useGmp && extension_loaded('gmp');
    }

    public static function hasBcMath()
    {
        return self::$useBcMath && extension_loaded('bcmath');
    }
}
