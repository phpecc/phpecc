<?php

namespace Mdanter\Ecc;

/**
 * Temporary class to handle GMP/BCMath loading while reefactorisation is under way.
 * @author thibaud
 *
 */

final class ModuleConfig
{
    private static $useGmp = false;
    
    private static $useBcMath = true;
    
    public static function useGmp() {
        self::$useGmp = true;
        self::$useBcMath = false;
    }
    
    public static function useBcMath() {
        self::$useGmp = false;
        self::$useBcMath = true;
    }
    
    public static function hasGmp() {
        return self::$useGmp && extension_loaded('gmp'); 
    }
    
    public static function hasBcMath() {
        return self::$useBcMath && extension_loaded('bcmath');
    }
}