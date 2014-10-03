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

/*
 * This class is where the elliptic curve arithmetic takes place. The important methods are: - add: adds two points according to ec arithmetic - double: doubles a point on the ec field mod p - mul: uses double and add to achieve multiplication The rest of the methods are there for supporting the ones above.
 */
class Point implements PointInterface
{

    private $curve;

    private $x;

    private $y;

    private $order;

    private $adapter;

    public function __construct(CurveFpInterface $curve, $x, $y, $order = null, MathAdapter $adapter)
    {
        $this->curve = $curve;
        $this->x = $x;
        $this->y = $y;
        $this->order = $order;
        $this->adapter = $adapter;

        if (! $this->curve->contains($this->x, $this->y)) {
            throw new \RuntimeException("Curve" . print_r($this->curve, true) . " does not contain point ( " . $x . " , " . $y . " )");
        }

        if ($this->order != null) {
            if ($this->cmp($this->mul($order), Points::infinity()) != 0) {
                throw new \RuntimeException("SELF * ORDER MUST EQUAL INFINITY.");
            }
        }
    }

    public function cmp(PointInterface $other)
    {
        if ($other->equals(Points::infinity())) {
            return 1;
        }

        $math = $this->adapter;

        $equal  = $math->cmp($this->x, $other->getX());
        $equal &= $math->cmp($this->y, $other->getY());
        $equal &= $math->cmp($this->curve, $other->getCurve());

        if ($equal) {
            return 0;
        }

        return 1;
    }

    public function equals(PointInterface $other)
    {
        return $this->cmp($other) == 0;
    }

    public function add(PointInterface $addend)
    {
        if ($addend->equals(Points::infinity())) {
            return $this;
        }

        $math = $this->adapter;

        if ($this->curve->equals($addend->getCurve())) {
            if ($math->mod($math->cmp($this->x, $addend->getX()), $this->curve->getPrime()) == 0) {
                if ($math->mod($math->add($this->y, $addend->getY()), $this->curve->getPrime()) == 0) {
                    return Points::infinity();
                }

                return $this->getDouble();
            }

            $p = $this->getCurve()->getPrime();
            $l = $math->mul($math->sub($addend->getY(), $this->y), NumberTheory::inverseMod($math->sub($addend->getX(), $this->x), $p));
            $x3 = $math->mod($math->sub($math->sub($math->pow($l, 2), $this->x), $addend->getX()), $p);
            $y3 = $math->mod($math->sub($math->mul($l, $math->sub($this->x, $x3)), $this->y), $p);

            return new Point($this->curve, $x3, $y3, null, $this->adapter);
        }
        else {
            throw new \RuntimeException("The Elliptic Curves do not match.");
        }
    }

    public function mul($multiplier)
    {
        $math = $this->adapter;
        $e = $multiplier;

        if ($this->order != null) {
            $e = $math->mod($e, $this->order);
        }

        if ($math->cmp($e, 0) == 0) {
            return Points::infinity();
        }

        if ($math->cmp($e, 0) > 0) {
            $e3 = $math->mul(3, $e);

            $negative_self = new Point($this->curve, $this->x, $math->sub(0, $this->y), $this->order, $this->adapter);
            $i = $math->div($this->calcleftMostBit($e3), 2);

            $result = $this;

            while ($math->cmp($i, 1) > 0) {
                $result = $result->getDouble();

                $e3bit = $math->cmp($math->bitwiseAnd($e3, $i), 0);
                $ebit = $math->cmp($math->bitwiseAnd($e, $i), 0);

                if ($e3bit != 0 && $ebit == 0) {
                    $result = $result->add($this);
                } elseif ($e3bit == 0 && $ebit != 0) {
                    $result = $result->add($negative_self);
                }

                $i = $math->div($i, 2);
            }

            return $result;
        }
    }

    private function calcLeftMostBit($x)
    {
        $math = $this->adapter;

        if ($math->cmp($x, 0) > 0) {
            $result = 1;

            while ($math->cmp($result, $x) <= 0) {
                $result = $math->mul(2, $result);
            }

            return $math->div($result, 2);
        }
    }

    public function getCurve()
    {
        return $this->curve;
    }

    public function __toString()
    {
        return "(" . $this->x . "," . $this->y . ")";
    }


    public function getDouble()
    {
        $math = $this->adapter;

        $p = $this->curve->getPrime();
        $a = $this->curve->getA();

        $inverse = NumberTheory::inverseMod($math->mul(2, $this->y), $p);
        $threeX2 = $math->mul(3, $math->pow($this->x, 2));

        $l = $math->mod($math->mul($math->add($threeX2, $a), $inverse), $p);
        $x3 = $math->mod($math->sub($math->pow($l, 2), $math->mul(2, $this->x)), $p);
        $y3 = $math->mod($math->sub($math->mul($l, $math->sub($this->x, $x3)), $this->y), $p);

        if ($math->cmp(0, $y3) > 0) {
            $y3 = $math->add($p, $y3);
        }

        return new self($this->curve, $x3, $y3, null, $this->adapter);
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }
}
