<?php

namespace Mdanter\Ecc;

/**
 * *********************************************************************
 * Copyright (C) 2012 Matyas Danter
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES
 * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 * ***********************************************************************
 */

/**
 * The gmp extension in PHP does not implement certain necessary operations
 * for elliptic curve encryption
 * This class implements all neccessary static methods
 */
class GmpUtils
{

    public static function gmp_mod2($n, $d)
    {
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $res = gmp_div_r($n, $d);
            if (gmp_cmp(0, $res) > 0) {
                $res = gmp_add($d, $res);
            }
            return gmp_strval($res);
        } else {
            throw new \RuntimeException("PLEASE INSTALL GMP");
        }
    }

    public static function gmp_random($n)
    {
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $random = gmp_strval(gmp_random());
            $small_rand = rand();
            while (gmp_cmp($random, $n) > 0) {
                $random = gmp_div($random, $small_rand, GMP_ROUND_ZERO);
            }
            
            return gmp_strval($random);
        } else {
            throw new \RuntimeException("PLEASE INSTALL GMP");
        }
    }

    public static function gmp_hexdec($hex)
    {
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $dec = gmp_strval(gmp_init($hex, 16), 10);
            
            return $dec;
        } else {
            throw new \RuntimeException("PLEASE INSTALL GMP");
        }
    }

    public static function gmp_dechex($dec)
    {
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $hex = gmp_strval(gmp_init($dec, 10), 16);
            
            return $hex;
        } else {
            throw new \RuntimeException("PLEASE INSTALL GMP");
        }
    }
}
?>
