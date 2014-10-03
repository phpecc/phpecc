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
 * This class serves as public - private key exchange for signature verification.
 */
class PrivateKey implements PrivateKeyInterface
{

    private $public_key;

    private $secret_multiplier;

    public function __construct(PublicKey $public_key, $secret_multiplier)
    {
        $this->public_key = $public_key;
        $this->secret_multiplier = $secret_multiplier;
    }

    public function sign($hash, $random_k)
    {
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $G = $this->public_key->getGenerator();
            $n = $G->getOrder();
            $k = GmpUtils::gmpMod2($random_k, $n);
            $p1 = Point::mul($k, $G);
            $r = $p1->getX();
            
            if (gmp_cmp($r, 0) == 0) {
                throw new \RuntimeException("error: random number R = 0 <br />");
            }
            
            $s = GmpUtils::gmpMod2(gmp_mul(NumberTheory::inverseMod($k, $n), GmpUtils::gmpMod2(gmp_add($hash, gmp_mul($this->secret_multiplier, $r)), $n)), $n);
            
            if (gmp_cmp($s, 0) == 0) {
                throw new \RuntimeException("error: random number S = 0<br />");
            }
            
            return new Signature($r, $s);
        } elseif (\Mdanter\Ecc\ModuleConfig::hasBcMath()) {
            $G = $this->public_key->getGenerator();
            $n = $G->getOrder();
            $k = bcmod($random_k, $n);
            $p1 = Point::mul($k, $G);
            $r = $p1->getX();
            
            if (bccomp($r, 0) == 0) {
                throw new \RuntimeException("error: random number R = 0 <br />");
            }
            
            $s = bcmod(bcmul(NumberTheory::inverseMod($k, $n), bcmod(bcadd($hash, bcmul($this->secret_multiplier, $r)), $n)), $n);
            
            if (bccomp($s, 0) == 0) {
                throw new \LogicException("error: random number S = 0<br />");
            }
            
            return new Signature($r, $s);
        } else {
            throw new \RuntimeException("Please install BCMATH or GMP");
        }
    }

    public static function intToString($x)
    {
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            if (gmp_cmp($x, 0) >= 0) {
                if (gmp_cmp($x, 0) == 0) {
                    return chr(0);
                }
                
                $result = "";
                
                while (gmp_cmp($x, 0) > 0) {
                    $q = gmp_div($x, 256, 0);
                    $r = GmpUtils::gmpMod2($x, 256);
                    $ascii = chr($r);
                    
                    $result = $ascii . $result;
                    $x = $q;
                }
                
                return $result;
            }
        } elseif (\Mdanter\Ecc\ModuleConfig::hasBcMath()) {
            if (bccomp($x, 0) != - 1) {
                if (bccomp($x, 0) == 0) {
                    return chr(0);
                }
                
                $result = "";
                
                while (bccomp($x, 0) == 1) {
                    $q = bcdiv($x, 256, 0);
                    $r = bcmod($x, 256);
                    $ascii = chr($r);
                    
                    $result = $ascii . $result;
                    $x = $q;
                }
                
                return $result;
            }
        } else {
            throw new \RuntimeException("Please install BCMATH or GMP");
        }
    }

    public static function stringToInt($s)
    {
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $result = 0;
            
            for ($c = 0; $c < strlen($s); $c ++) {
                $result = gmp_add(gmp_mul(256, $result), ord($s[$c]));
            }
            
            return $result;
        } elseif (\Mdanter\Ecc\ModuleConfig::hasBcMath()) {
            $result = 0;
            
            for ($c = 0; $c < strlen($s); $c ++) {
                $result = bcadd(bcmul(256, $result), ord($s[$c]));
            }
            
            return $result;
        } else {
            throw new \RuntimeException("Please install BCMATH or GMP");
        }
    }

    public static function digestInteger($m)
    {
        return self::stringToInt(hash('sha1', self::intToString($m), true));
    }

    public static function pointIsValid(Point $generator, $x, $y)
    {
        if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
            $n = $generator->getOrder();
            $curve = $generator->getCurve();
            
            if (gmp_cmp($x, 0) < 0 || gmp_cmp($n, $x) <= 0 || gmp_cmp($y, 0) < 0 || gmp_cmp($n, $y) <= 0) {
                return false;
            }
            
            $containment = $curve->contains($x, $y);
            
            if (! $containment) {
                return false;
            }
            
            $point = new Point($curve, $x, $y);
            $op = Point::mul($n, $point);
            
            if (! (Point::cmp($op, Point::$infinity) == 0)) {
                return false;
            }
            
            return true;
        } elseif (\Mdanter\Ecc\ModuleConfig::hasBcMath()) {
            $n = $generator->getOrder();
            $curve = $generator->getCurve();
            
            if (bccomp($x, 0) == - 1 || bccomp($n, $x) != 1 || bccomp($y, 0) == - 1 || bccomp($n, $y) != 1) {
                return false;
            }
            
            $containment = $curve->contains($x, $y);
            
            if (! $containment) {
                return false;
            }
            
            $point = new Point($curve, $x, $y);
            $op = Point::mul($n, $point);
            
            if (! (Point::cmp($op, Point::$infinity) == 0)) {
                return false;
            }
            
            return true;
        } else {
            throw new \RuntimeException("Please install BCMATH or GMP");
        }
    }
}
