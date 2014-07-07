<?php
/***********************************************************************
Copyright (C) 2012 Matyas Danter

Permission is hereby granted, free of charge, to any person obtaining 
a copy of this software and associated documentation files (the "Software"), 
to deal in the Software without restriction, including without limitation 
the rights to use, copy, modify, merge, publish, distribute, sublicense, 
and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included 
in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES 
OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, 
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR 
OTHER DEALINGS IN THE SOFTWARE.
*************************************************************************/

/**
 * This class encapsulates the NIST recommended curves
 *  - fields are Mersenne primes, i.e. for some p, Mersenne_prine = 2^p - 1
 *
 * @author Matej Danter
 */
class NISTcurve {

    public static function curve_192() {
        // NIST Curve P-192:
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            $_p = '6277101735386680763835789423207666416083908700390324961279';
            $_r = '6277101735386680763835789423176059013767194773182842284081';
            $_b = gmp_Utils::gmp_hexdec('0x64210519e59c80e70fa7e9ab72243049feb8deecc146b9b1');
            $_Gx = gmp_Utils::gmp_hexdec('0x188da80eb03090f67cbf20eb43a18800f4ff0afd82ff1012');
            $_Gy = gmp_Utils::gmp_hexdec('0x07192b95ffc8da78631011ed6b24cdd573f977a11e794811');

            $curve_192 = new CurveFp($_p, -3, $_b);
            //$generator_192 = new Point( $curve_192, $_Gx, $_Gy, $_r );
        } else if (extension_loaded('bcmath') && USE_EXT == 'BCMATH') {

            $_p = '6277101735386680763835789423207666416083908700390324961279';
            $_r = '6277101735386680763835789423176059013767194773182842284081';
            $_b = bcmath_Utils::bchexdec('0x64210519e59c80e70fa7e9ab72243049feb8deecc146b9b1');
            $_Gx = bcmath_Utils::bchexdec('0x188da80eb03090f67cbf20eb43a18800f4ff0afd82ff1012');
            $_Gy = bcmath_Utils::bchexdec('0x07192b95ffc8da78631011ed6b24cdd573f977a11e794811');

            $curve_192 = new CurveFp($_p, -3, $_b);
            //$generator_192 = new Point( $curve_192, $_Gx, $_Gy, $_r );
        }

        return $curve_192;
    }

    public static function curve_224() {
        # NIST Curve P-224:
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            $_p = '26959946667150639794667015087019630673557916260026308143510066298881';
            $_r = '26959946667150639794667015087019625940457807714424391721682722368061';
            $_b = gmp_Utils::gmp_hexdec('0xb4050a850c04b3abf54132565044b0b7d7bfd8ba270b39432355ffb4');
            $_Gx = gmp_Utils::gmp_hexdec('0xb70e0cbd6bb4bf7f321390b94a03c1d356c21122343280d6115c1d21');
            $_Gy = gmp_Utils::gmp_hexdec('0xbd376388b5f723fb4c22dfe6cd4375a05a07476444d5819985007e34');

            $curve_224 = new CurveFp($_p, -3, $_b);
            //$generator_224 = new Point( $curve_224, $_Gx, $_Gy, $_r );
        } else if (extension_loaded('bcmath') && USE_EXT == 'BCMATH') {
            $_p = '26959946667150639794667015087019630673557916260026308143510066298881';
            $_r = '26959946667150639794667015087019625940457807714424391721682722368061';
            $_b = bcmath_Utils::bchexdec('0xb4050a850c04b3abf54132565044b0b7d7bfd8ba270b39432355ffb4');
            $_Gx = bcmath_Utils::bchexdec('0xb70e0cbd6bb4bf7f321390b94a03c1d356c21122343280d6115c1d21');
            $_Gy = bcmath_Utils::bchexdec('0xbd376388b5f723fb4c22dfe6cd4375a05a07476444d5819985007e34');

            $curve_224 = new CurveFp($_p, -3, $_b);
            //$generator_224 = new Point( $curve_224, $_Gx, $_Gy, $_r );
        }
        return $curve_224;
    }

    public static function curve_256() {
        # NIST Curve P-256:
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            $_p = '115792089210356248762697446949407573530086143415290314195533631308867097853951';
            $_r = '115792089210356248762697446949407573529996955224135760342422259061068512044369';
            $_b = gmp_Utils::gmp_hexdec('0x5ac635d8aa3a93e7b3ebbd55769886bc651d06b0cc53b0f63bce3c3e27d2604b');
            $_Gx = gmp_Utils::gmp_hexdec('0x6b17d1f2e12c4247f8bce6e563a440f277037d812deb33a0f4a13945d898c296');
            $_Gy = gmp_Utils::gmp_hexdec('0x4fe342e2fe1a7f9b8ee7eb4a7c0f9e162bce33576b315ececbb6406837bf51f5');

            $curve_256 = new CurveFp($_p, -3, $_b);
            //$generator_256 = new Point( $curve_256, $_Gx, $_Gy, $_r );
        } else if (extension_loaded('bcmath') && USE_EXT == 'BCMATH') {
            $_p = '115792089210356248762697446949407573530086143415290314195533631308867097853951';
            $_r = '115792089210356248762697446949407573529996955224135760342422259061068512044369';
            $_b = bcmath_Utils::bchexdec('0x5ac635d8aa3a93e7b3ebbd55769886bc651d06b0cc53b0f63bce3c3e27d2604b');
            $_Gx = bcmath_Utils::bchexdec('0x6b17d1f2e12c4247f8bce6e563a440f277037d812deb33a0f4a13945d898c296');
            $_Gy = bcmath_Utils::bchexdec('0x4fe342e2fe1a7f9b8ee7eb4a7c0f9e162bce33576b315ececbb6406837bf51f5');

            $curve_256 = new CurveFp($_p, -3, $_b);
            //$generator_256 = new Point( $curve_256, $_Gx, $_Gy, $_r );
        }
        return $curve_256;
    }

    public static function curve_384() {
        # NIST Curve P-384:
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            $_p = '39402006196394479212279040100143613805079739270465446667948293404245721771496870329047266088258938001861606973112319';
            $_r = '39402006196394479212279040100143613805079739270465446667946905279627659399113263569398956308152294913554433653942643';
            $_b = gmp_Utils::gmp_hexdec('0xb3312fa7e23ee7e4988e056be3f82d19181d9c6efe8141120314088f5013875ac656398d8a2ed19d2a85c8edd3ec2aef');
            $_Gx = gmp_Utils::gmp_hexdec('0xaa87ca22be8b05378eb1c71ef320ad746e1d3b628ba79b9859f741e082542a385502f25dbf55296c3a545e3872760ab7');
            $_Gy = gmp_Utils::gmp_hexdec('0x3617de4a96262c6f5d9e98bf9292dc29f8f41dbd289a147ce9da3113b5f0b8c00a60b1ce1d7e819d7a431d7c90ea0e5f');

            $curve_384 = new CurveFp($_p, -3, $_b);
            //$generator_384 = new Point( $curve_384, $_Gx, $_Gy, $_r );
        } else if (extension_loaded('bcmath') && USE_EXT == 'BCMATH') {
            $_p = '39402006196394479212279040100143613805079739270465446667948293404245721771496870329047266088258938001861606973112319';
            $_r = '39402006196394479212279040100143613805079739270465446667946905279627659399113263569398956308152294913554433653942643';
            $_b = bcmath_Utils::bchexdec('0xb3312fa7e23ee7e4988e056be3f82d19181d9c6efe8141120314088f5013875ac656398d8a2ed19d2a85c8edd3ec2aef');
            $_Gx = bcmath_Utils::bchexdec('0xaa87ca22be8b05378eb1c71ef320ad746e1d3b628ba79b9859f741e082542a385502f25dbf55296c3a545e3872760ab7');
            $_Gy = bcmath_Utils::bchexdec('0x3617de4a96262c6f5d9e98bf9292dc29f8f41dbd289a147ce9da3113b5f0b8c00a60b1ce1d7e819d7a431d7c90ea0e5f');

            $curve_384 = new CurveFp($_p, -3, $_b);
            //$generator_384 = new Point( $curve_384, $_Gx, $_Gy, $_r );
        }
        return $curve_384;
    }

    public static function curve_521() {
        # NIST Curve P-521:
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            $_p = '6864797660130609714981900799081393217269435300143305409394463459185543183397656052122559640661454554977296311391480858037121987999716643812574028291115057151';
            $_r = '6864797660130609714981900799081393217269435300143305409394463459185543183397655394245057746333217197532963996371363321113864768612440380340372808892707005449';
            $_b = gmp_Utils::gmp_hexdec('0x051953eb9618e1c9a1f929a21a0b68540eea2da725b99b315f3b8b489918ef109e156193951ec7e937b1652c0bd3bb1bf073573df883d2c34f1ef451fd46b503f00');
            $_Gx = gmp_Utils::gmp_hexdec('0xc6858e06b70404e9cd9e3ecb662395b4429c648139053fb521f828af606b4d3dbaa14b5e77efe75928fe1dc127a2ffa8de3348b3c1856a429bf97e7e31c2e5bd66');
            $_Gy = gmp_Utils::gmp_hexdec('0x11839296a789a3bc0045c8a5fb42c7d1bd998f54449579b446817afbd17273e662c97ee72995ef42640c550b9013fad0761353c7086a272c24088be94769fd16650');

            $curve_521 = new CurveFp($_p, -3, $_b);
            //$generator_521 = new Point( $curve_521, $_Gx, $_Gy, $_r );
        } else if (extension_loaded('bcmath') && USE_EXT == 'BCMATH') {
            $_p = '6864797660130609714981900799081393217269435300143305409394463459185543183397656052122559640661454554977296311391480858037121987999716643812574028291115057151';
            $_r = '6864797660130609714981900799081393217269435300143305409394463459185543183397655394245057746333217197532963996371363321113864768612440380340372808892707005449';
            $_b = bcmath_Utils::bchexdec('0x051953eb9618e1c9a1f929a21a0b68540eea2da725b99b315f3b8b489918ef109e156193951ec7e937b1652c0bd3bb1bf073573df883d2c34f1ef451fd46b503f00');
            $_Gx = bcmath_Utils::bchexdec('0xc6858e06b70404e9cd9e3ecb662395b4429c648139053fb521f828af606b4d3dbaa14b5e77efe75928fe1dc127a2ffa8de3348b3c1856a429bf97e7e31c2e5bd66');
            $_Gy = bcmath_Utils::bchexdec('0x11839296a789a3bc0045c8a5fb42c7d1bd998f54449579b446817afbd17273e662c97ee72995ef42640c550b9013fad0761353c7086a272c24088be94769fd16650');

            $curve_521 = new CurveFp($_p, -3, $_b);
            //$generator_521 = new Point( $curve_521, $_Gx, $_Gy, $_r );
        }
        return $curve_521;
    }

    public static function generator_192() {
        // NIST Curve P-192:
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            $_p = '6277101735386680763835789423207666416083908700390324961279';
            $_r = '6277101735386680763835789423176059013767194773182842284081';
            $_b = gmp_Utils::gmp_hexdec('0x64210519e59c80e70fa7e9ab72243049feb8deecc146b9b1');
            $_Gx = gmp_Utils::gmp_hexdec('0x188da80eb03090f67cbf20eb43a18800f4ff0afd82ff1012');
            $_Gy = gmp_Utils::gmp_hexdec('0x07192b95ffc8da78631011ed6b24cdd573f977a11e794811');

            $curve_192 = new CurveFp($_p, -3, $_b);
            $generator_192 = new Point($curve_192, $_Gx, $_Gy, $_r);
        } else if (extension_loaded('bcmath') && USE_EXT == 'BCMATH') {
            $_p = '6277101735386680763835789423207666416083908700390324961279';
            $_r = '6277101735386680763835789423176059013767194773182842284081';
            $_b = bcmath_Utils::bchexdec('0x64210519e59c80e70fa7e9ab72243049feb8deecc146b9b1');
            $_Gx = bcmath_Utils::bchexdec('0x188da80eb03090f67cbf20eb43a18800f4ff0afd82ff1012');
            $_Gy = bcmath_Utils::bchexdec('0x07192b95ffc8da78631011ed6b24cdd573f977a11e794811');

            $curve_192 = new CurveFp($_p, -3, $_b);
            $generator_192 = new Point($curve_192, $_Gx, $_Gy, $_r);
        }
        return $generator_192;
    }

    public static function generator_224() {
        # NIST Curve P-224:
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            $_p = '26959946667150639794667015087019630673557916260026308143510066298881';
            $_r = '26959946667150639794667015087019625940457807714424391721682722368061';
            $_b = gmp_Utils::gmp_hexdec('0xb4050a850c04b3abf54132565044b0b7d7bfd8ba270b39432355ffb4');
            $_Gx = gmp_Utils::gmp_hexdec('0xb70e0cbd6bb4bf7f321390b94a03c1d356c21122343280d6115c1d21');
            $_Gy = gmp_Utils::gmp_hexdec('0xbd376388b5f723fb4c22dfe6cd4375a05a07476444d5819985007e34');

            $curve_224 = new CurveFp($_p, -3, $_b);
            $generator_224 = new Point($curve_224, $_Gx, $_Gy, $_r);
        } else if (extension_loaded('bcmath') && USE_EXT == 'BCMATH') {
            $_p = '26959946667150639794667015087019630673557916260026308143510066298881';
            $_r = '26959946667150639794667015087019625940457807714424391721682722368061';
            $_b = bcmath_Utils::bchexdec('0xb4050a850c04b3abf54132565044b0b7d7bfd8ba270b39432355ffb4');
            $_Gx = bcmath_Utils::bchexdec('0xb70e0cbd6bb4bf7f321390b94a03c1d356c21122343280d6115c1d21');
            $_Gy = bcmath_Utils::bchexdec('0xbd376388b5f723fb4c22dfe6cd4375a05a07476444d5819985007e34');

            $curve_224 = new CurveFp($_p, -3, $_b);
            $generator_224 = new Point($curve_224, $_Gx, $_Gy, $_r);
        }
        return $generator_224;
    }

    public static function generator_256() {
        # NIST Curve P-256:
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            $_p = '115792089210356248762697446949407573530086143415290314195533631308867097853951';
            $_r = '115792089210356248762697446949407573529996955224135760342422259061068512044369';
            $_b = gmp_Utils::gmp_hexdec('0x5ac635d8aa3a93e7b3ebbd55769886bc651d06b0cc53b0f63bce3c3e27d2604b');
            $_Gx = gmp_Utils::gmp_hexdec('0x6b17d1f2e12c4247f8bce6e563a440f277037d812deb33a0f4a13945d898c296');
            $_Gy = gmp_Utils::gmp_hexdec('0x4fe342e2fe1a7f9b8ee7eb4a7c0f9e162bce33576b315ececbb6406837bf51f5');

            $curve_256 = new CurveFp($_p, -3, $_b);
            $generator_256 = new Point($curve_256, $_Gx, $_Gy, $_r);
        } else if (extension_loaded('bcmath') && USE_EXT == 'BCMATH') {
            $_p = '115792089210356248762697446949407573530086143415290314195533631308867097853951';
            $_r = '115792089210356248762697446949407573529996955224135760342422259061068512044369';
            $_b = bcmath_Utils::bchexdec('0x5ac635d8aa3a93e7b3ebbd55769886bc651d06b0cc53b0f63bce3c3e27d2604b');
            $_Gx = bcmath_Utils::bchexdec('0x6b17d1f2e12c4247f8bce6e563a440f277037d812deb33a0f4a13945d898c296');
            $_Gy = bcmath_Utils::bchexdec('0x4fe342e2fe1a7f9b8ee7eb4a7c0f9e162bce33576b315ececbb6406837bf51f5');

            $curve_256 = new CurveFp($_p, -3, $_b);
            $generator_256 = new Point($curve_256, $_Gx, $_Gy, $_r);
        }
        return $generator_256;
    }

    public static function generator_384() {
        # NIST Curve P-384:
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            $_p = '39402006196394479212279040100143613805079739270465446667948293404245721771496870329047266088258938001861606973112319';
            $_r = '39402006196394479212279040100143613805079739270465446667946905279627659399113263569398956308152294913554433653942643';
            $_b = gmp_Utils::gmp_hexdec('0xb3312fa7e23ee7e4988e056be3f82d19181d9c6efe8141120314088f5013875ac656398d8a2ed19d2a85c8edd3ec2aef');
            $_Gx = gmp_Utils::gmp_hexdec('0xaa87ca22be8b05378eb1c71ef320ad746e1d3b628ba79b9859f741e082542a385502f25dbf55296c3a545e3872760ab7');
            $_Gy = gmp_Utils::gmp_hexdec('0x3617de4a96262c6f5d9e98bf9292dc29f8f41dbd289a147ce9da3113b5f0b8c00a60b1ce1d7e819d7a431d7c90ea0e5f');

            $curve_384 = new CurveFp($_p, -3, $_b);
            $generator_384 = new Point($curve_384, $_Gx, $_Gy, $_r);
        } else if (extension_loaded('bcmath') && USE_EXT == 'BCMATH') {
            $_p = '39402006196394479212279040100143613805079739270465446667948293404245721771496870329047266088258938001861606973112319';
            $_r = '39402006196394479212279040100143613805079739270465446667946905279627659399113263569398956308152294913554433653942643';
            $_b = bcmath_Utils::bchexdec('0xb3312fa7e23ee7e4988e056be3f82d19181d9c6efe8141120314088f5013875ac656398d8a2ed19d2a85c8edd3ec2aef');
            $_Gx = bcmath_Utils::bchexdec('0xaa87ca22be8b05378eb1c71ef320ad746e1d3b628ba79b9859f741e082542a385502f25dbf55296c3a545e3872760ab7');
            $_Gy = bcmath_Utils::bchexdec('0x3617de4a96262c6f5d9e98bf9292dc29f8f41dbd289a147ce9da3113b5f0b8c00a60b1ce1d7e819d7a431d7c90ea0e5f');

            $curve_384 = new CurveFp($_p, -3, $_b);
            $generator_384 = new Point($curve_384, $_Gx, $_Gy, $_r);
        }
        return $generator_384;
    }

    public static function generator_521() {
        # NIST Curve P-521:
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            $_p = '6864797660130609714981900799081393217269435300143305409394463459185543183397656052122559640661454554977296311391480858037121987999716643812574028291115057151';
            $_r = '6864797660130609714981900799081393217269435300143305409394463459185543183397655394245057746333217197532963996371363321113864768612440380340372808892707005449';
            $_b = gmp_Utils::gmp_hexdec('0x051953eb9618e1c9a1f929a21a0b68540eea2da725b99b315f3b8b489918ef109e156193951ec7e937b1652c0bd3bb1bf073573df883d2c34f1ef451fd46b503f00');
            $_Gx = gmp_Utils::gmp_hexdec('0xc6858e06b70404e9cd9e3ecb662395b4429c648139053fb521f828af606b4d3dbaa14b5e77efe75928fe1dc127a2ffa8de3348b3c1856a429bf97e7e31c2e5bd66');
            $_Gy = gmp_Utils::gmp_hexdec('0x11839296a789a3bc0045c8a5fb42c7d1bd998f54449579b446817afbd17273e662c97ee72995ef42640c550b9013fad0761353c7086a272c24088be94769fd16650');

            $curve_521 = new CurveFp($_p, -3, $_b);
            $generator_521 = new Point($curve_521, $_Gx, $_Gy, $_r);
        } else if (extension_loaded('bcmath') && USE_EXT == 'BCMATH') {
            $_p = '6864797660130609714981900799081393217269435300143305409394463459185543183397656052122559640661454554977296311391480858037121987999716643812574028291115057151';
            $_r = '6864797660130609714981900799081393217269435300143305409394463459185543183397655394245057746333217197532963996371363321113864768612440380340372808892707005449';
            $_b = bcmath_Utils::bchexdec('0x051953eb9618e1c9a1f929a21a0b68540eea2da725b99b315f3b8b489918ef109e156193951ec7e937b1652c0bd3bb1bf073573df883d2c34f1ef451fd46b503f00');
            $_Gx = bcmath_Utils::bchexdec('0xc6858e06b70404e9cd9e3ecb662395b4429c648139053fb521f828af606b4d3dbaa14b5e77efe75928fe1dc127a2ffa8de3348b3c1856a429bf97e7e31c2e5bd66');
            $_Gy = bcmath_Utils::bchexdec('0x11839296a789a3bc0045c8a5fb42c7d1bd998f54449579b446817afbd17273e662c97ee72995ef42640c550b9013fad0761353c7086a272c24088be94769fd16650');

            $curve_521 = new CurveFp($_p, -3, $_b);
            $generator_521 = new Point($curve_521, $_Gx, $_Gy, $_r);
        }
        return $generator_521;
    }

}
?>
