<?php

namespace Mdanter\Ecc\Crypto\Key;

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

use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Primitives\CurveFpInterface;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Primitives\PointInterface;

/**
 * This class serves as public- private key exchange for signature verification
 */
class PublicKey implements PublicKeyInterface
{
    /**
     *
     * @var CurveFpInterface
     */
    protected $curve;

    /**
     *
     * @var GeneratorPoint
     */
    protected $generator;

    /**
     *
     * @var PointInterface
     */
    protected $point;

    /**
     *
     * @var GmpMathInterface
     */
    protected $adapter;

    /**
     * Initialize a new instance.
     *
     * @param  GmpMathInterface  $adapter
     * @param  GeneratorPoint    $generator
     * @param  PointInterface    $point
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function __construct(GmpMathInterface $adapter, GeneratorPoint $generator, PointInterface $point)
    {
        $this->curve = $generator->getCurve();
        $this->generator = $generator;
        $this->point = $point;
        $this->adapter = $adapter;

        $n = $generator->getOrder();

        if ($adapter->cmp($point->getX(), gmp_init(0, 10)) < 0 || $adapter->cmp($n, $point->getX()) <= 0
            || $adapter->cmp($point->getY(), gmp_init(0, 10)) < 0 || $adapter->cmp($n, $point->getY()) <= 0
        ) {
            throw new \RuntimeException("Generator point has x and y out of range.");
        }
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\Key\PublicKeyInterface::getCurve()
     */
    public function getCurve()
    {
        return $this->curve;
    }

    /**
     * {$inheritDoc}
     * @see \Mdanter\Ecc\Crypto\Key\PublicKeyInterface::getGenerator()
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\Key\PublicKeyInterface::getPoint()
     */
    public function getPoint()
    {
        return $this->point;
    }
}
