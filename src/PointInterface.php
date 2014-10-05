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
 * and operations over the points on the Elliptic Curve.
 *
 * @author Matej Danter
 */
interface PointInterface
{
    /**
     *
     * @param PointInterface $addend
     * @return PointInterface
     */
    public function add(PointInterface $addend);

    /**
     * @param PointInterface $other
     * @return int
     */
    public function cmp(PointInterface $other);

    /**
     *
     * @param PointInterface $other
     * @return bool
     */
    public function equals(PointInterface $other);

    /**
     *
     * @param mixed $multiplier
     * @return PointInterface
     */
    public function mul($multiplier);

    /**
     * @return CurveFpInterface
     */
    public function getCurve();

    /**
     * @return PointInterface
     */
    public function getDouble();

    /**
     * @return number
     */
    public function getOrder();

    /**
     * @return number
     */
    public function getX();

    /**
     * @return number
     */
    public function getY();

    /**
     * @return string
     */
    public function __toString();

}
