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
 * This is the contract for implementing Point, which encapsulates entities
 * and operations over the points on the Elliptic Curve. Implementations must be immutable.
 *
 * Implementors must be wary of the special "Infinity" implementation, which breaks LSP, and should always
 * be checked against (and properly handled) when receiving a PointInterface as an argument or when a method indicates it can
 * return infinity.
 *
 * @todo Fix LSP break (possibly derive an extra interface, FinitePointInterface from current one, and move
 * coordinate-related ops to sub-interface).
 *
 * @author Matej Danter
 */
interface PointInterface
{
    /**
     * Adds another point to the current one and returns the resulting point.
     *
     * @param PointInterface $addend
     * @return PointInterface|Infinity
     */
    public function add(PointInterface $addend);

    /**
     * Compares the current instance to another point.
     *
     * @param PointInterface|Infinity $other
     * @return int|string A number less than 0 when current instance is less than the given point, 0 when they are equal,
     * and greater than 0 when current instance is greater than the given point.
     */
    public function cmp(PointInterface $other);

    /**
     * Checks whether the current instance is equal to the given point.
     *
     * @param PointInterface|Infinity $other
     * @return bool true when points are equal, false otherwise.
     */
    public function equals(PointInterface $other);

    /**
     * Multiplies the point by a scalar value and returns the resulting point.
     *
     * @param mixed $multiplier
     * @return PointInterface|Infinity
     */
    public function mul($multiplier);

    /**
     * Returns the curve to which the point belongs.
     *
     * @return CurveFpInterface
     */
    public function getCurve();

    /**
     * Doubles the current point and returns the resulting point.
     *
     * @return PointInterface|Infinity
     */
    public function getDouble();

    /**
     * Returns the order of the point.
     *
     * @return int|string
     */
    public function getOrder();

    /**
     * Returns the X coordinate of the point.
     *
     * @return int|string
     */
    public function getX();

    /**
     * Returns the Y coordinate of the point.
     *
     * @return int|string
     */
    public function getY();

    /**
     * Returns the string representation of the point.
     *
     * @return string
     */
    public function __toString();
}
