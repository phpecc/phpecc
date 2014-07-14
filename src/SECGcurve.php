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
 *
 * @author Jan Moritz Lindemann
 */
class SECGcurve
{

    public static function curve256k1()
    {
        // Secp256k1 Curve
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $_p = GmpUtils::gmpHexDec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F');
            $_a = 0;
            $_b = 7;

            $curve256k1 = new CurveFp($_p, $_a, $_b);
;
        } elseif (\Mdanter\Ecc\ModuleConfig::hasBcMath()) {
            $_p = BcMathUtils::bchexdec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F');
            $_a = 0;
            $_b = 7;

            $curve256k1 = new CurveFp($_p, $_a, $_b);
        } else {
            throw new \RuntimeException('Please install GMP or BCMath extensions.');
        }

        return $curve256k1;
    }

    public static function curve256r1()
    {
        // Secp256r1 Curve
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $_p = GmpUtils::gmpHexDec('0xFFFFFFFF00000001000000000000000000000000FFFFFFFFFFFFFFFFFFFFFFFF');
            $_a = GmpUtils::gmpHexDec('0xFFFFFFFF00000001000000000000000000000000FFFFFFFFFFFFFFFFFFFFFFFC');
            $_b = GmpUtils::gmpHexDec('0x5AC635D8AA3A93E7B3EBBD55769886BC651D06B0CC53B0F63BCE3C3E27D2604B');

            $curve256r1 = new CurveFp($_p, $_a, $_b);
            ;
        } elseif (\Mdanter\Ecc\ModuleConfig::hasBcMath()) {
            $_p = BcMathUtils::bchexdec('0xFFFFFFFF00000001000000000000000000000000FFFFFFFFFFFFFFFFFFFFFFFF');
            $_a = BcMathUtils::bchexdec('0xFFFFFFFF00000001000000000000000000000000FFFFFFFFFFFFFFFFFFFFFFFC');
            $_b = BcMathUtils::bchexdec('0x5AC635D8AA3A93E7B3EBBD55769886BC651D06B0CC53B0F63BCE3C3E27D2604B');

            $curve256r1 = new CurveFp($_p, $_a, $_b);
        } else {
            throw new \RuntimeException('Please install GMP or BCMath extensions.');
        }

        return $curve256r1;
    }

    public static function curve384r1()
    {
        // Secp384r1 Curve
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $_p = GmpUtils::gmpHexDec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFFFF0000000000000000FFFFFFFF');
            $_a = GmpUtils::gmpHexDec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFFFF0000000000000000FFFFFFFC');
            $_b = GmpUtils::gmpHexDec('0xB3312FA7E23EE7E4988E056BE3F82D19181D9C6EFE8141120314088F5013875AC656398D8A2ED19D2A85C8EDD3EC2AEF');

            $curve384r1 = new CurveFp($_p, $_a, $_b);
            ;
        } elseif (\Mdanter\Ecc\ModuleConfig::hasBcMath()) {
            $_p = BcMathUtils::bchexdec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFFFF0000000000000000FFFFFFFF');
            $_a = BcMathUtils::bchexdec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFFFF0000000000000000FFFFFFFC');
            $_b = BcMathUtils::bchexdec('0xB3312FA7E23EE7E4988E056BE3F82D19181D9C6EFE8141120314088F5013875AC656398D8A2ED19D2A85C8EDD3EC2AEF');

            $curve384r1 = new CurveFp($_p, $_a, $_b);
        } else {
            throw new \RuntimeException('Please install GMP or BCMath extensions.');
        }

        return $curve384r1;
    }

    public static function curve521r1()
    {
        // Secp521r1 Curve
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $_p = GmpUtils::gmpHexDec('0x01FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF');
            $_a = GmpUtils::gmpHexDec('0x01FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFC');
            $_b = GmpUtils::gmpHexDec('0x0051953EB9618E1C9A1F929A21A0B68540EEA2DA725B99B315F3B8B489918EF109E156193951EC7E937B1652C0BD3BB1BF073573DF883D2C34F1EF451FD46B503F00');

            $curve521r1 = new CurveFp($_p, $_a, $_b);
            ;
        } elseif (\Mdanter\Ecc\ModuleConfig::hasBcMath()) {
            $_p = BcMathUtils::bchexdec('0x01FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF');
            $_a = BcMathUtils::bchexdec('0x01FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFC');
            $_b = BcMathUtils::bchexdec('0x0051953EB9618E1C9A1F929A21A0B68540EEA2DA725B99B315F3B8B489918EF109E156193951EC7E937B1652C0BD3BB1BF073573DF883D2C34F1EF451FD46B503F00');

            $curve521r1 = new CurveFp($_p, $_a, $_b);
        } else {
            throw new \RuntimeException('Please install GMP or BCMath extensions.');
        }

        return $curve521r1;
    }

    public static function generator256k1()
    {
        // Secp256k1 Curve
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $_p = GmpUtils::gmpHexDec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F');
            $_a = 0;
            $_b = 7;
            $_r = GmpUtils::gmpHexDec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141');
            $_Gx = GmpUtils::gmpHexDec('0x79BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798');
            $_Gy = GmpUtils::gmpHexDec('0x483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8');
            
            $curve256k1 = new CurveFp($_p, $_a, $_b);
            $generator256k1 = new Point($curve256k1, $_Gx, $_Gy, $_r);
        } elseif (\Mdanter\Ecc\ModuleConfig::hasBcMath()) {
            $_p = BcMathUtils::bchexdec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F');
            $_a = 0;
            $_b = 7;
            $_r = BcMathUtils::bchexdec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141');
            $_Gx = BcMathUtils::bchexdec('0x79BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798');
            $_Gy = BcMathUtils::bchexdec('0x483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8');
            
            $curve256k1 = new CurveFp($_p, $_a, $_b);
            $generator256k1 = new Point($curve256k1, $_Gx, $_Gy, $_r);
        } else {
            throw new \RuntimeException('Please install GMP or BCMath extensions.');
        }
        
        return $generator256k1;
    }

    public static function generator256r1()
    {
        // Secp256r1 Curve
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $_p = GmpUtils::gmpHexDec('0xFFFFFFFF00000001000000000000000000000000FFFFFFFFFFFFFFFFFFFFFFFF');
            $_a = GmpUtils::gmpHexDec('0xFFFFFFFF00000001000000000000000000000000FFFFFFFFFFFFFFFFFFFFFFFC');
            $_b = GmpUtils::gmpHexDec('0x5AC635D8AA3A93E7B3EBBD55769886BC651D06B0CC53B0F63BCE3C3E27D2604B');
            $_r = GmpUtils::gmpHexDec('0xFFFFFFFF00000000FFFFFFFFFFFFFFFFBCE6FAADA7179E84F3B9CAC2FC632551');
            $_Gx = GmpUtils::gmpHexDec('0x6B17D1F2E12C4247F8BCE6E563A440F277037D812DEB33A0F4A13945D898C296');
            $_Gy = GmpUtils::gmpHexDec('0x4FE342E2FE1A7F9B8EE7EB4A7C0F9E162BCE33576B315ECECBB6406837BF51F5');

            $curve256r1 = new CurveFp($_p, $_a, $_b);
            $generator256r1 = new Point($curve256r1, $_Gx, $_Gy, $_r);
        } elseif (\Mdanter\Ecc\ModuleConfig::hasBcMath()) {
            $_p = BcMathUtils::bchexdec('0xFFFFFFFF00000001000000000000000000000000FFFFFFFFFFFFFFFFFFFFFFFF');
            $_a = BcMathUtils::bchexdec('0xFFFFFFFF00000001000000000000000000000000FFFFFFFFFFFFFFFFFFFFFFFC');
            $_b = BcMathUtils::bchexdec('0x5AC635D8AA3A93E7B3EBBD55769886BC651D06B0CC53B0F63BCE3C3E27D2604B');
            $_r = BcMathUtils::bchexdec('0xFFFFFFFF00000000FFFFFFFFFFFFFFFFBCE6FAADA7179E84F3B9CAC2FC632551');
            $_Gx = BcMathUtils::bchexdec('0x6B17D1F2E12C4247F8BCE6E563A440F277037D812DEB33A0F4A13945D898C296');
            $_Gy = BcMathUtils::bchexdec('0x4FE342E2FE1A7F9B8EE7EB4A7C0F9E162BCE33576B315ECECBB6406837BF51F5');

            $curve256r1 = new CurveFp($_p, $_a, $_b);
            $generator256r1 = new Point($curve256r1, $_Gx, $_Gy, $_r);
        } else {
            throw new \RuntimeException('Please install GMP or BCMath extensions.');
        }

        return $generator256r1;
    }

    public static function generator384r1()
    {
        // Secp384r1 Curve
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $_p = GmpUtils::gmpHexDec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFFFF0000000000000000FFFFFFFF');
            $_a = GmpUtils::gmpHexDec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFFFF0000000000000000FFFFFFFC');
            $_b = GmpUtils::gmpHexDec('0xB3312FA7E23EE7E4988E056BE3F82D19181D9C6EFE8141120314088F5013875AC656398D8A2ED19D2A85C8EDD3EC2AEF');
            $_r = GmpUtils::gmpHexDec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFC7634D81F4372DDF581A0DB248B0A77AECEC196ACCC52973');
            $_Gx = GmpUtils::gmpHexDec('0xAA87CA22BE8B05378EB1C71EF320AD746E1D3B628BA79B9859F741E082542A385502F25DBF55296C3A545E3872760AB7');
            $_Gy = GmpUtils::gmpHexDec('0x3617DE4A96262C6F5D9E98BF9292DC29F8F41DBD289A147CE9DA3113B5F0B8C00A60B1CE1D7E819D7A431D7C90EA0E5F');

            $curve384r1 = new CurveFp($_p, $_a, $_b);
            $generator384r1 = new Point($curve384r1, $_Gx, $_Gy, $_r);
        } elseif (\Mdanter\Ecc\ModuleConfig::hasBcMath()) {
            $_p = BcMathUtils::bchexdec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFFFF0000000000000000FFFFFFFF');
            $_a = BcMathUtils::bchexdec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFFFF0000000000000000FFFFFFFC');
            $_b = BcMathUtils::bchexdec('0xB3312FA7E23EE7E4988E056BE3F82D19181D9C6EFE8141120314088F5013875AC656398D8A2ED19D2A85C8EDD3EC2AEF');
            $_r = BcMathUtils::bchexdec('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFC7634D81F4372DDF581A0DB248B0A77AECEC196ACCC52973');
            $_Gx = BcMathUtils::bchexdec('0xAA87CA22BE8B05378EB1C71EF320AD746E1D3B628BA79B9859F741E082542A385502F25DBF55296C3A545E3872760AB7');
            $_Gy = BcMathUtils::bchexdec('0x3617DE4A96262C6F5D9E98BF9292DC29F8F41DBD289A147CE9DA3113B5F0B8C00A60B1CE1D7E819D7A431D7C90EA0E5F');

            $curve384r1 = new CurveFp($_p, $_a, $_b);
            $generator384r1 = new Point($curve384r1, $_Gx, $_Gy, $_r);
        } else {
            throw new \RuntimeException('Please install GMP or BCMath extensions.');
        }

        return $generator384r1;
    }

    public static function generator521r1()
    {
        // Secp521r1 Curve
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $_p = GmpUtils::gmpHexDec('0x01FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF');
            $_a = GmpUtils::gmpHexDec('0x01FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFC');
            $_b = GmpUtils::gmpHexDec('0x0051953EB9618E1C9A1F929A21A0B68540EEA2DA725B99B315F3B8B489918EF109E156193951EC7E937B1652C0BD3BB1BF073573DF883D2C34F1EF451FD46B503F00');
            $_r = GmpUtils::gmpHexDec('0x01FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFA51868783BF2F966B7FCC0148F709A5D03BB5C9B8899C47AEBB6FB71E91386409');
            $_Gx = GmpUtils::gmpHexDec('0xAA87CA22BE8B05378EB1C71EF320AD746E1D3B628BA79B9859F741E082542A385502F25DBF55296C3A545E3872760AB7');
            $_Gy = GmpUtils::gmpHexDec('0x3617DE4A96262C6F5D9E98BF9292DC29F8F41DBD289A147CE9DA3113B5F0B8C00A60B1CE1D7E819D7A431D7C90EA0E5F');

            $curve521r1 = new CurveFp($_p, $_a, $_b);
            $generator521r1 = new Point($curve521r1, $_Gx, $_Gy, $_r);
        } elseif (\Mdanter\Ecc\ModuleConfig::hasBcMath()) {
            $_p = BcMathUtils::bchexdec('0x01FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF');
            $_a = BcMathUtils::bchexdec('0x01FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFC');
            $_b = BcMathUtils::bchexdec('0x0051953EB9618E1C9A1F929A21A0B68540EEA2DA725B99B315F3B8B489918EF109E156193951EC7E937B1652C0BD3BB1BF073573DF883D2C34F1EF451FD46B503F00');
            $_r = BcMathUtils::bchexdec('0x01FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFA51868783BF2F966B7FCC0148F709A5D03BB5C9B8899C47AEBB6FB71E91386409');
            $_Gx = BcMathUtils::bchexdec('0xAA87CA22BE8B05378EB1C71EF320AD746E1D3B628BA79B9859F741E082542A385502F25DBF55296C3A545E3872760AB7');
            $_Gy = BcMathUtils::bchexdec('0x3617DE4A96262C6F5D9E98BF9292DC29F8F41DBD289A147CE9DA3113B5F0B8C00A60B1CE1D7E819D7A431D7C90EA0E5F');

            $curve521r1 = new CurveFp($_p, $_a, $_b);
            $generator521r1 = new Point($curve521r1, $_Gx, $_Gy, $_r);
        } else {
            throw new \RuntimeException('Please install GMP or BCMath extensions.');
        }

        return $generator521r1;
    }
}
