<?php

namespace Mdanter\Ecc\Primitives;

use Mdanter\Ecc\Math\ModularArithmetic;
use Mdanter\Ecc\Math\MathAdapterInterface;

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
 * This class is where the elliptic curve arithmetic takes place.
 * The important methods are:
 * - add: adds two points according to ec arithmetic
 * - double: doubles a point on the ec field mod p
 * - mul: uses double and add to achieve multiplication The rest of the methods are there for supporting the ones above.
 */
class Point implements PointInterface
{
    /**
     * @var CurveFpInterface
     */
    private $curve;

    /**
     * @var MathAdapterInterface
     */
    private $adapter;

    /**
     * @var ModularArithmetic
     */
    private $modAdapter;

    /**
     * @var string
     */
    private $x;

    /**
     * @var string
     */
    private $y;

    /**
     * @var string
     */
    private $order;

    /**
     * @var bool
     */
    private $infinity = false;

    /**
     * Initialize a new instance
     *
     * @param MathAdapterInterface $adapter
     * @param CurveFpInterface     $curve
     * @param int|string           $x
     * @param int|string           $y
     * @param int|string           $order
     * @param bool                 $infinity
     *
     * @throws \RuntimeException    when either the curve does not contain the given coordinates or
     *                                      when order is not null and P(x, y) * order is not equal to infinity.
     */
    public function __construct(MathAdapterInterface $adapter, CurveFpInterface $curve, $x, $y, $order, $infinity = false)
    {
        $this->adapter    = $adapter;
        $this->modAdapter = $curve->getModAdapter();
        $this->curve      = $curve;
        $this->x          = (string) $x;
        $this->y          = (string) $y;
        $this->order      = $order !== null ? (string) $order : '0';
        $this->infinity   = (bool) $infinity;

        if (! $infinity && ! $curve->contains($x, $y)) {
            throw new \RuntimeException("Curve " . $curve . " does not contain point (" . $x . ", " . $y . ")");
        }

        if ($order != null && ! $this->mul($order)->isInfinity()) {
            throw new \RuntimeException("SELF * ORDER MUST EQUAL INFINITY. (" . (string)$this->mul($order) . " found instead)");
        }
    }

    /**
     * @return MathAdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\PointInterface::isInfinity()
     */
    public function isInfinity()
    {
        return (bool) $this->infinity;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\PointInterface::getCurve()
     */
    public function getCurve()
    {
        return $this->curve;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\PointInterface::getOrder()
     */
    public function getOrder()
    {
        return (string) $this->order;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\PointInterface::getX()
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\PointInterface::getY()
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\PointInterface::add()
     */
    public function add(PointInterface $addend)
    {
        if (! $this->curve->equals($addend->getCurve())) {
            throw new \RuntimeException("The Elliptic Curves do not match.");
        }

        if ($addend->isInfinity()) {
            return clone $this;
        }

        if ($this->isInfinity()) {
            return clone $addend;
        }

        $math = $this->adapter;
        $modMath = $this->modAdapter;

        if ($math->cmp($addend->getX(), $this->x) == 0) {
            if ($math->cmp($addend->getY(), $this->y) == 0) {
                return $this->getDouble();
            } else {
                return new self($this->adapter, $this->curve, 0, 0, 0, true);
            }
        }

        $slope = $modMath->div(
            $math->sub($addend->getY(), $this->y),
            $math->sub($addend->getX(), $this->x)
        );

        $xR = $modMath->sub(
            $math->sub($math->pow($slope, 2), $this->x),
            $addend->getX()
        );

        $yR = $modMath->sub(
            $math->mul($slope, $math->sub($this->x, $xR)),
            $this->y
        );

        return new self($this->adapter, $this->curve, $xR, $yR, $this->order, false);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\PointInterface::cmp()
     */
    public function cmp(PointInterface $other)
    {
        if ($other->isInfinity() && $this->isInfinity()) {
            return 0;
        }

        if ($other->isInfinity() || $this->isInfinity()) {
            return 1;
        }

        $math = $this->adapter;
        $equal = ($math->cmp($this->x, $other->getX()) == 0);
        $equal &= ($math->cmp($this->y, $other->getY()) == 0);
        $equal &= $this->isInfinity() == $other->isInfinity();
        $equal &= $this->curve->equals($other->getCurve());

        if ($equal) {
            return 0;
        }

        return 1;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\PointInterface::equals()
     */
    public function equals(PointInterface $other)
    {
        return $this->cmp($other) == 0;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\PointInterface::mul()
     */
    public function mul($n)
    {
        if ($this->isInfinity()) {
            return $this->curve->getInfinity();
        }

        if ($this->adapter->cmp($this->order, '0') > 0) {
            $n = $this->adapter->mod($n, $this->order);
        }

        if ($this->adapter->cmp($n, '0') == 0) {
            return $this->curve->getInfinity();
        }

        $r = [
            $this->curve->getInfinity(),
            clone $this
        ];

        $n = $this->adapter->baseConvert($n, 10, 2);
        $k = strlen($n);

        for ($i = 0; $i < $k; $i++) {
            $j = $n[$i];

            $this->cswap($r[0], $r[1], $j ^ 1);

            $r[0] = $r[0]->add($r[1]);
            $r[1] = $r[1]->getDouble();

            $this->cswap($r[0], $r[1], $j ^ 1);
        }

        $r[0]->validate();

        return $r[0];
    }

    /**
     * @param Point $a
     * @param Point $b
     * @param $cond
     */
    private function cswap(self $a, self $b, $cond)
    {
        $this->cswapValue($a->x, $b->x, $cond);
        $this->cswapValue($a->y, $b->y, $cond);
        $this->cswapValue($a->order, $b->order, $cond);
        $this->cswapValue($a->infinity, $b->infinity, $cond);
    }

    /**
     * @param $a
     * @param $b
     * @param $cond
     */
    public function cswapValue(& $a, & $b, $cond)
    {
        $size = max(strlen($this->adapter->baseConvert($a, 10, 2)), strlen($this->adapter->baseConvert($b, 10, 2)));

        $mask = 1 - intval($cond);
        $mask = str_pad('', $size, $mask, STR_PAD_LEFT);
        $mask = $this->adapter->baseConvert($mask, 2, 10);

        $tA = $this->adapter->bitwiseAnd($a, $mask);
        $tB = $this->adapter->bitwiseAnd($b, $mask);

        $a = $this->adapter->bitwiseXor($this->adapter->bitwiseXor($a, $b), $tB);
        $b = $this->adapter->bitwiseXor($this->adapter->bitwiseXor($a, $b), $tA);
        $a = $this->adapter->bitwiseXor($this->adapter->bitwiseXor($a, $b), $tB);
    }

    /**
     *
     */
    private function validate()
    {
        if (! $this->infinity && ! $this->curve->contains($this->x, $this->y)) {
            throw new \RuntimeException('Invalid point');
        }
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\PointInterface::getDouble()
     */
    public function getDouble()
    {
        if ($this->isInfinity()) {
            return $this->curve->getInfinity();
        }

        $math = $this->adapter;
        $modMath = $this->modAdapter;

        $a = $this->curve->getA();
        $threeX2 = $math->mul(3, $math->pow($this->x, 2));

        $tangent  = $modMath->div(
            $math->add($threeX2, $a),
            $math->mul(2, $this->y)
        );

        $x3 = $modMath->sub(
            $math->pow($tangent, 2),
            $math->mul(2, $this->x)
        );

        $y3 = $modMath->sub(
            $math->mul($tangent, $math->sub($this->x, $x3)),
            $this->y
        );

        return new self($this->adapter, $this->curve, $x3, $y3, $this->order);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\PointInterface::__toString()
     */
    public function __toString()
    {
        if ($this->infinity) {
            return '[ (infinity) on ' . (string) $this->curve . ' ]';
        }

        return "[ (" . $this->adapter->toString($this->x) . "," . $this->adapter->toString($this->y) . ') on ' . (string) $this->curve . ' ]';
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        if ($this->infinity) {
            return [
                'x' => 'inf (' . $this->x . ')',
                'y' => 'inf (' . $this->y . ')',
                'z' => 'inf (' . $this->order . ')',
                'curve' => $this->curve
            ];
        }

        return [
            'x' => (string) $this->x,
            'y' => (string) $this->y,
            'z' => (string) $this->order,
            'curve' => $this->curve
        ];
    }
}
