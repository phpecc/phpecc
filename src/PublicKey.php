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
     * @var MathAdapter
     */
    protected $adapter;

    /**
     * Initialize a new instance.
     *
     * @param GeneratorPoint $generator
     * @param PointInterface $point
     * @param MathAdapter $adapter
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function __construct(GeneratorPoint $generator, PointInterface $point, MathAdapter $adapter)
    {
        $this->curve = $generator->getCurve();
        $this->generator = $generator;
        $this->point = $point;
        $this->adapter = $adapter;

        $n = $generator->getOrder();

        if ($n == null) {
            throw new \LogicException("Generator must have order.");
        }

        if (! $point->mul($n)->equals(Points::infinity())) {
            throw new \RuntimeException("Generator point order is bad.");
        }

        if ($adapter->cmp($point->getX(), 0) < 0 || $adapter->cmp($n, $point->getX()) <= 0 ||
            $adapter->cmp($point->getY(), 0) < 0 || $adapter->cmp($n, $point->getY()) <= 0) {
            throw new \RuntimeException("Generator point has x and y out of range.");
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PublicKeyInterface::verifies()
     */
    public function verifies($hash, SignatureInterface $signature)
    {
        $math = $this->adapter;

        $G = $this->generator;
        $n = $this->generator->getOrder();
        $point = $this->point;

        $r = $signature->getR();
        $s = $signature->getS();

        if ($math->cmp($r, 1) < 1 || $math->cmp($r, $math->sub($n, 1)) > 0) {
            return false;
        }

        if ($math->cmp($s, 1) < 1 || $math->cmp($s, $math->sub($n, 1)) > 0) {
            return false;
        }

        $c = $math->inverseMod($s, $n);
        $u1 = $math->mod($math->mul($hash, $c), $n);
        $u2 = $math->mod($math->mul($r, $c), $n);
        $xy = $G->mul($u1)->add($point->mul($u2));
        $v = $math->mod($xy->getX(), $n);

        return $math->cmp($v, $r) == 0;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PublicKeyInterface::getCurve()
     */
    public function getCurve()
    {
        return $this->curve;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PublicKeyInterface::getGenerator()
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PublicKeyInterface::getPoint()
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PublicKeyInterface::getPrivateKey()
     */
    public function getPrivateKey($secretMultiplier)
    {
        return new PrivateKey($this, $secretMultiplier, $this->adapter);
    }
}
