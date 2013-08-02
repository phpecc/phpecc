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


/*
 * This class is where the elliptic curve arithmetic takes place.
 *
 * The important methods are:
 *      - add: adds two points according to ec arithmetic
 *      - double: doubles a point on the ec field mod p
 *      - mul: uses double and add to achieve multiplication
 *
 * The rest of the methods are there for supporting the ones above.
 */

class Point implements PointInterface {

        public $curve;
        public $x;
        public $y;
        public $order;
        public static $infinity = 'infinity';

        public function __construct(CurveFp $curve, $x, $y, $order = null) {
            $this->curve = $curve;
            $this->x = $x;
            $this->y = $y;
            $this->order = $order;


            if (isset($this->curve) && ($this->curve instanceof CurveFp)) {
                if (!$this->curve->contains($this->x, $this->y)) {
                    throw new ErrorException("Curve" . print_r($this->curve, true) . " does not contain point ( " . $x . " , " . $y . " )");
                    
                }

                if ($this->order != null) {

                    if (self::cmp(self::mul($order, $this), self::$infinity) != 0) {
                        throw new ErrorException("SELF * ORDER MUST EQUAL INFINITY.");
                    }
                }
            }
        }

        public static function cmp($p1, $p2) {
            if (extension_loaded('gmp') && USE_EXT=='GMP') {
                if (!($p1 instanceof Point)) {
                    if (($p2 instanceof Point))
                        return 1;
                    if (!($p2 instanceof Point))
                        return 0;
                }

                if (!($p2 instanceof Point)) {
                    if (($p1 instanceof Point))
                        return 1;
                    if (!($p1 instanceof Point))
                        return 0;
                }

                if (gmp_cmp($p1->x, $p2->x) == 0 && gmp_cmp($p1->y, $p2->y) == 0 && CurveFp::cmp($p1->curve, $p2->curve)) {
                    return 0;
                } else {
                    return 1;
                }
            } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
                if (!($p1 instanceof Point)) {
                    if (($p2 instanceof Point))
                        return 1;
                    if (!($p2 instanceof Point))
                        return 0;
                }

                if (!($p2 instanceof Point)) {
                    if (($p1 instanceof Point))
                        return 1;
                    if (!($p1 instanceof Point))
                        return 0;
                }

                if (bccomp($p1->x, $p2->x) == 0 && bccomp($p1->y, $p2->y) == 0 && CurveFp::cmp($p1->curve, $p2->curve)) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                throw new ErrorException("Please install BCMATH or GMP");
            }
        }

        public static function add($p1, $p2) {

            if (self::cmp($p2, self::$infinity) == 0 && ($p1 instanceof Point)) {
                return $p1;
            }
            if (self::cmp($p1, self::$infinity) == 0 && ($p2 instanceof Point)) {
                return $p2;
            }

            if (self::cmp($p1, self::$infinity) == 0 && self::cmp($p2, self::$infinity) == 0) {
                return self::$infinity;
            }

            if (extension_loaded('gmp') && USE_EXT=='GMP') {


                if (CurveFp::cmp($p1->curve, $p2->curve) == 0) {
                    if (gmp_Utils::gmp_mod2(gmp_cmp($p1->x, $p2->x), $p1->curve->getPrime()) == 0) {
                        if (gmp_Utils::gmp_mod2(gmp_add($p1->y, $p2->y), $p1->curve->getPrime()) == 0) {
                            return self::$infinity;
                        } else {
                            return self::double($p1);
                        }
                    }

                    $p = $p1->curve->getPrime();

                    $l = gmp_strval(gmp_mul(gmp_sub($p2->y, $p1->y), NumberTheory::inverse_mod(gmp_sub($p2->x, $p1->x), $p)));


                    $x3 = gmp_strval(gmp_Utils::gmp_mod2(gmp_sub(gmp_sub(gmp_pow($l, 2), $p1->x), $p2->x), $p));


                    $y3 = gmp_strval(gmp_Utils::gmp_mod2(gmp_sub(gmp_mul($l, gmp_sub($p1->x, $x3)), $p1->y), $p));


                    $p3 = new Point($p1->curve, $x3, $y3);


                    return $p3;
                } else {
                    throw new ErrorException("The Elliptic Curves do not match.");
                }
            } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {

                if (CurveFp::cmp($p1->curve, $p2->curve) == 0) {
                    if (bcmod(bccomp($p1->x, $p2->x), $p1->curve->getPrime()) == 0) {
                        if (bcmod(bcadd($p1->y, $p2->y), $p1->curve->getPrime()) == 0) {
                            return self::$infinity;
                        } else {
                            return self::double($p1);
                        }
                    }

                    $p = $p1->curve->getPrime();

                    $l = bcmod(bcmul(bcsub($p2->y, $p1->y), NumberTheory::inverse_mod(bcsub($p2->x, $p1->x), $p)), $p);


                    $x3 = bcmod(bcsub(bcsub(bcpow($l, 2), $p1->x), $p2->x), $p);
                    $step0 = bcsub($p1->x, $x3);
                    $step1 = bcmul($l, $step0);
                    $step2 = bcsub($step1, $p1->y);
                    $step3 = bcmod($step2, $p);

                    $y3 = bcmod(bcsub(bcmul($l, bcsub($p1->x, $x3)), $p1->y), $p);
                    if (bccomp(0, $y3) == 1)
                        $y3 = bcadd($p, $y3);

                    $p3 = new Point($p1->curve, $x3, $y3);


                    return $p3;
                }else {
                    throw new ErrorException("The Elliptic Curves do not match.");
                }
            } else {
                throw new ErrorException("Please install BCMATH or GMP");
            }
        }

        public static function mul($x2, Point $p1) {
            if (extension_loaded('gmp') && USE_EXT=='GMP') {
                $e = $x2;

                if (self::cmp($p1, self::$infinity) == 0) {
                    return self::$infinity;
                }
                if ($p1->order != null) {
                    $e = gmp_strval(gmp_Utils::gmp_mod2($e, $p1->order));
                }
                if (gmp_cmp($e, 0) == 0) {

                    return self::$infinity;
                }

                $e = gmp_strval($e);

                if (gmp_cmp($e, 0) > 0) {

                    $e3 = gmp_mul(3, $e);

                    $negative_self = new Point($p1->curve, $p1->x, gmp_strval(gmp_sub(0, $p1->y)), $p1->order);
                    $i = gmp_div(self::leftmost_bit($e3), 2);

                    $result = $p1;

                    while (gmp_cmp($i, 1) > 0) {

                        $result = self::double($result);

                        $e3bit = gmp_cmp(gmp_and($e3, $i), 0);
                        $ebit = gmp_cmp(gmp_and($e, $i), 0);
                        
                        if ($e3bit != 0 && $ebit == 0) {

                            $result = self::add($result, $p1);
                        }else if ($e3bit == 0 && $ebit != 0) {
                            $result = self::add($result, $negative_self);
                        }

                        $i = gmp_strval(gmp_div($i, 2));
                    }
                    return $result;
                }
            } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
                $e = $x2;

                if (self::cmp($p1, self::$infinity) == 0) {
                    return self::$infinity;
                }

                if ($p1->order != null) {

                    $e = bcmod($e, $p1->order);
                }

                if (bccomp($e, 0) == 0) {
                    return self::$infinity;
                }

                if (bccomp($e, 0) == 1) {
                    $e3 = bcmul(3, $e);

                    $negative_self = new Point($p1->curve, $p1->x, bcsub(0, $p1->y), $p1->order);
                    $i = bcdiv(self::leftmost_bit($e3), 2);

                    $result = $p1;


                    while (bccomp($i, 1) == 1) {
                        $result = self::double($result);

                        $e3bit = bccomp(bcmath_Utils::bcand($e3, $i), '0');
                        $ebit = bccomp(bcmath_Utils::bcand($e, $i), '0');
                        
                        if ($e3bit != 0 && $ebit == 0) {
                            $result = self::add($result, $p1);
                        }else if ($e3bit == 0 && $ebit != 0) {
                            $result = self::add($result, $negative_self);
                        }

                        $i = bcdiv($i, 2);
                    }
                    return $result;
                }
            } else {
                throw new ErrorException("Please install BCMATH or GMP");
            }
        }

        public static function leftmost_bit($x) {
            if (extension_loaded('gmp') && USE_EXT=='GMP') {
                if (gmp_cmp($x, 0) > 0) {
                    $result = 1;
                    while (gmp_cmp($result, $x) < 0 || gmp_cmp($result, $x) == 0) {
                        $result = gmp_mul(2, $result);
                    }
                    return gmp_strval(gmp_div($result, 2));
                }
            } else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {
                if (bccomp($x, 0) == 1) {
                    $result = 1;
                    while (bccomp($result, $x) == -1 || bccomp($result, $x) == 0) {
                        $result = bcmul(2, $result);
                    }
                    return bcdiv($result, 2);
                }
            } else {
                throw new ErrorException("Please install BCMATH or GMP");
            }
        }

        public static function rmul(Point $x1, $m) {
            return self::mul($m, $x1);
        }

        public function __toString() {
            if (!($this instanceof Point) && $this == self::$infinity)
                return self::$infinity;
            return "(" . $this->x . "," . $this->y . ")";
        }

        public static function double(Point $p1) {


            if (extension_loaded('gmp') && USE_EXT=='GMP') {

                $p = $p1->curve->getPrime();
                $a = $p1->curve->getA();

                $inverse = NumberTheory::inverse_mod(gmp_strval(gmp_mul(2, $p1->y)), $p);

                $three_x2 = gmp_mul(3, gmp_pow($p1->x, 2));

                $l = gmp_strval(gmp_Utils::gmp_mod2(gmp_mul(gmp_add($three_x2, $a), $inverse), $p));

                $x3 = gmp_strval(gmp_Utils::gmp_mod2(gmp_sub(gmp_pow($l, 2), gmp_mul(2, $p1->x)), $p));

                $y3 = gmp_strval(gmp_Utils::gmp_mod2(gmp_sub(gmp_mul($l, gmp_sub($p1->x, $x3)), $p1->y), $p));

                if (gmp_cmp(0, $y3) > 0)
                    $y3 = gmp_strval(gmp_add($p, $y3));

                $p3 = new Point($p1->curve, $x3, $y3);

                return $p3;
            }else if (extension_loaded('bcmath') && USE_EXT=='BCMATH') {

                $p = $p1->curve->getPrime();
                $a = $p1->curve->getA();

                $inverse = NumberTheory::inverse_mod(bcmul(2, $p1->y), $p);


                $three_x2 = bcmul(3, bcpow($p1->x, 2));

                $l = bcmod(bcmul(bcadd($three_x2, $a), $inverse), $p);

                $x3 = bcmod(bcsub(bcpow($l, 2), bcmul(2, $p1->x)), $p);

                $y3 = bcmod(bcsub(bcmul($l, bcsub($p1->x, $x3)), $p1->y), $p);

                if (bccomp(0, $y3) == 1)
                    $y3 = bcadd($p, $y3);

                $p3 = new Point($p1->curve, $x3, $y3);

                return $p3;
            } else {
                throw new ErrorException("Please install BCMATH or GMP");
            }
        }

        public function getX() {
            return $this->x;
        }

        public function getY() {
            return $this->y;
        }

        public function getCurve() {
            return $this->curve;
        }

        public function getOrder() {
            return $this->order;
        }

    }

?>
