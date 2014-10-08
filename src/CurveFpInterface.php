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
 * This is the contract for implementing CurveFp (EC prime finite-field).
 *
 * @author Matej Danter
 */
interface CurveFpInterface
{

    /**
     * Returns the point identified by given coordinates.
     *
     * @param int|string $x
     * @param int|string $y
     * @param int|string $order
     * @return PointInterface
     */
    public function getPoint($x, $y, $order = null);

    /**
     * Checks whether the curve contains the given coordinates.
     *
     * @param int|string $x
     * @param int|string $y
     * @return bool
     */
    public function contains($x, $y);

    /**
     * Returns the a parameter of the curve.
     *
     * @return int|string
     */
    public function getA();

    /**
     * Returns the b parameter of the curve.
     *
     * @return int|string
     */
    public function getB();

    /**
     * Returns the prime associated with the curve.
     *
     * @return int|string
     */
    public function getPrime();

    /**
     * Compares the curve to another.
     *
     * @param CurveFpInterface $other
     * @return int < 0 if $this < $other, 0 if $other == $this, > 0 if $this > $other
     */
    public function cmp(CurveFpInterface $other);

    /**
     * Checks whether the curve is equal to another.
     *
     * @param CurveFpInterface $other
     * @return bool
     */
    public function equals(CurveFpInterface $other);
}
