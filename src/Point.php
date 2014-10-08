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
 * This class is where the elliptic curve arithmetic takes place. The important methods are:
 * - add: adds two points according to ec arithmetic
 * - double: doubles a point on the ec field mod p
 * - mul: uses double and add to achieve multiplication The rest of the methods are there for supporting the ones above.
 *
 * @author Matej Danter
 */
class Point implements PointInterface
{

    /**
     *
     * @var CurveFpInterface
     */
    private $curve;

    /**
     *
     * @var int|string
     */
    private $x;

    /**
     *
     * @var int|string
     */
    private $y;

    /**
     *
     * @var int|string
     */
    private $order;

    /**
     *
     * @var MathAdapter
     */
    private $adapter;

    /**
     * Initialize a new instance
     *
     * @param CurveFpInterface $curve
     * @param int|string $x
     * @param int|string $y
     * @param int|string $order
     * @param MathAdapter $adapter
     * @throws \RuntimeException when either the curve does not contain the given coordinates or
     * when order is not null and P(x, y) * order is not equal to infinity.
     */
    public function __construct(CurveFpInterface $curve, $x, $y, $order = null, MathAdapter $adapter)
    {
        $this->curve = $curve;
        $this->x = $x;
        $this->y = $y;
        $this->order = $order;
        $this->adapter = $adapter;

        if (! $this->curve->contains($this->x, $this->y)) {
            throw new \RuntimeException(
                "Curve " . $this->curve . " does not contain point (" . $x . ", " . $y . ")");
        }

        if ($this->order != null && ! $this->mul($order)->equals(Points::infinity())) {
            throw new \RuntimeException(
                "SELF * ORDER MUST EQUAL INFINITY. (" . (string)$this->mul($order) . " found instead)");
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::cmp()
     */
    public function cmp(PointInterface $other)
    {
        if ($other->equals(Points::infinity())) {
            return 1;
        }

        $math = $this->adapter;

        $equal  = ($math->cmp($this->x, $other->getX()) == 0);
        $equal &= ($math->cmp($this->y, $other->getY()) == 0);
        $equal &= $this->curve->equals($other->getCurve());

        if ($equal) {
            return 0;
        }

        return 1;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::equals()
     */
    public function equals(PointInterface $other)
    {
        return $this->cmp($other) == 0;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::add()
     */
    public function add(PointInterface $addend)
    {
        if ($addend->equals(Points::infinity())) {
            return $this;
        }

        $math = $this->adapter;

        if (! $this->curve->equals($addend->getCurve())) {
            throw new \RuntimeException("The Elliptic Curves do not match.");
        }

        if ($math->mod($math->cmp($this->x, $addend->getX()), $this->curve->getPrime()) == 0) {
            if ($math->mod($math->add($this->y, $addend->getY()), $this->curve->getPrime()) == 0) {
                return Points::infinity();
            }
            else {
                return $this->getDouble();
            }
        }

        $p = $this->curve->getPrime();
        $l = $math->mod(
            $math->mul($math->sub($addend->getY(), $this->y),
                $math->inverseMod($math->sub($addend->getX(), $this->x), $p)), $p);

        $x3 = $math->mod($math->sub($math->sub($math->pow($l, 2), $this->x), $addend->getX()), $p);
        $y3 = $math->mod($math->sub($math->mul($l, $math->sub($this->x, $x3)), $this->y), $p);

        if ($math->cmp(0, $y3) > 0) {
            $y3 = $math->add($p, $y3);
        }

        return $this->curve->getPoint($x3, $y3);
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::mul()
     */
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

            $negative_self = $this->curve->getPoint($this->x, $math->sub(0, $this->y), $this->order);
            $i = $math->div($this->calcleftMostBit($e3), 2);

            $result = $this;

            while ($math->cmp($i, 1) > 0) {
                $result = $result->getDouble();

                $e3bit = $math->cmp($math->bitwiseAnd($e3, $i), '0');
                $ebit = $math->cmp($math->bitwiseAnd($e, $i), '0');

                if ($e3bit != 0 && $ebit == 0) {
                    $result = $result->add($this);
                }
                elseif ($e3bit == 0 && $ebit != 0) {
                    $result = $result->add($negative_self);
                }

                $i = $math->div($i, 2);
            }

            return $result;
        }

        throw new \RuntimeException('Unable to multiply by ' . $multiplier);
    }

    /**
     *
     * @param int|string $x
     * @throws \RuntimeException
     */
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

        throw new \RuntimeException('Unable to get leftmost bit of ' . $math->toString($x));
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::getCurve()
     */
    public function getCurve()
    {
        return $this->curve;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::__toString()
     */
    public function __toString()
    {
        return "(" . $this->adapter->toString($this->x) . "," . $this->adapter->toString($this->y) . ")";
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::getDouble()
     */
    public function getDouble()
    {
        $math = $this->adapter;

        $p = $this->curve->getPrime();
        $a = $this->curve->getA();

        $inverse = $math->inverseMod($math->mul(2, $this->y), $p);
        $threeX2 = $math->mul(3, $math->pow($this->x, 2));

        $l = $math->mod($math->mul($math->add($threeX2, $a), $inverse), $p);
        $x3 = $math->mod($math->sub($math->pow($l, 2), $math->mul(2, $this->x)), $p);
        $y3 = $math->mod($math->sub($math->mul($l, $math->sub($this->x, $x3)), $this->y), $p);

        if ($math->cmp(0, $y3) > 0) {
            $y3 = $math->add($p, $y3);
        }

        return new self($this->curve, $x3, $y3, null, $this->adapter);
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::getOrder()
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::getX()
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::getY()
     */
    public function getY()
    {
        return $this->y;
    }
}
