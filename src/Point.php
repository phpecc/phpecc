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
 * This class is where the elliptic curve arithmetic takes place.
 * The important methods are:
 * - add: adds two points according to ec arithmetic
 * - double: doubles a point on the ec field mod p
 * - mul: uses double and add to achieve multiplication The rest of the methods are there for supporting the ones above.
 *
 */
class Point extends UnsafePoint
{

    /**
     * Initialize a new instance
     *
     * @param  CurveFpInterface     $curve
     * @param  int|string           $x
     * @param  int|string           $y
     * @param  int|string           $order
     * @param  MathAdapterInterface $adapter
     * @throws \RuntimeException    when either the curve does not contain the given coordinates or
     *                                      when order is not null and P(x, y) * order is not equal to infinity.
     */
    public function __construct(MathAdapterInterface $adapter, CurveFpInterface $curve, $x, $y, $order = null)
    {
        parent::__construct($adapter, $curve, $x, $y, $order, false);

        if (! $curve->contains($x, $y)) {
            throw new \RuntimeException("Curve " . $curve . " does not contain point (" . $x . ", " . $y . ")");
        }

        if ($order != null && ! $this->mul($order)->isInfinity()) {
            throw new \RuntimeException("SELF * ORDER MUST EQUAL INFINITY. (" . (string)$this->mul($order) . " found instead)");
        }
    }
}
