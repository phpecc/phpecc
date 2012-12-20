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
 * This class is a representation of an EC over a field modulo a prime number
 *
 * Important objectives for this class are:
 *  - Does the curve contain a point?
 *  - Comparison of two curves.
 */
class CurveFp implements CurveFpInterface {

    //Elliptic curve over the field of integers modulo a prime
    protected $a = 0;
    protected $b = 0;
    protected $prime = 0;

    //constructor that sets up the instance variables
    public function __construct($prime, $a, $b) {
        $this->a = $a;
        $this->b = $b;
        $this->prime = $prime;
    }

    public function contains($x, $y) {
        $eq_zero = null;

        if (extension_loaded('gmp') && USE_EXT=='GMP') {

            $eq_zero = gmp_cmp(gmp_Utils::gmp_mod2(gmp_sub(gmp_pow($y, 2), gmp_add(gmp_add(gmp_pow($x, 3), gmp_mul($this->a, $x)), $this->b)), $this->prime), 0);


            if ($eq_zero == 0) {
                return true;
            } else {
                return false;
            }
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {

            $eq_zero = bccomp(bcmod(bcsub(bcpow($y, 2), bcadd(bcadd(bcpow($x, 3), bcmul($this->a, $x)), $this->b)), $this->prime), 0);
            if ($eq_zero == 0) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

    public function getA() {
        return $this->a;
    }

    public function getB() {
        return $this->b;
    }

    public function getPrime() {
        return $this->prime;
    }

    public static function cmp(CurveFp $cp1, CurveFp $cp2) {
        $same = null;

        if (extension_loaded('gmp') && USE_EXT=='GMP') {

            if (gmp_cmp($cp1->a, $cp2->a) == 0 && gmp_cmp($cp1->b, $cp2->b) == 0 && gmp_cmp($cp1->prime, $cp2->prime) == 0) {
                return 0;
            } else {
                return 1;
            }
        } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
            if (bccomp($cp1->a, $cp2->a) == 0 && bccomp($cp1->b, $cp2->b) == 0 && bccomp($cp1->prime, $cp2->prime) == 0) {
                return 0;
            } else {
                return 1;
            }
        } else {
            throw new ErrorException("Please install BCMATH or GMP");
        }
    }

}
?>
