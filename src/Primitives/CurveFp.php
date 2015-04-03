<?php

/***********************************************************************
Copyright (C) 2012 Matyas Danter

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the "Software"),
to deal in the Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish, distribute, sublicense,
and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included
in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES
OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
*************************************************************************/
namespace Mdanter\Ecc\Primitives;

use Mdanter\Ecc\Math\ModularArithmetic;
use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Random\RandomNumberGeneratorInterface;

/**
 * This class is a representation of an EC over a field modulo a prime number
 *
 * Important objectives for this class are:
 * - Does the curve contain a point?
 * - Comparison of two curves.
 */
class CurveFp implements CurveFpInterface
{
    /**
     * Elliptic curve over the field of integers modulo a prime.
     *
     * @var int|string
     */
    protected $a = 0;

    /**
     *
     * @var int|string
     */
    protected $b = 0;

    /**
     *
     * @var int|string
     */
    protected $prime = 0;

    /**
     *
     * @var MathAdapterInterface
     */
    protected $adapter = null;

    /**
     *
     * @var ModularArithmetic
     */
    protected $modAdapter = null;

    /**
     * Constructor that sets up the instance variables.
     *
     * @param $prime int|string
     * @param $a int|string
     * @param $b int|string
     * @param $adapter MathAdapterInterface
     */
    public function __construct($prime, $a, $b, MathAdapterInterface $adapter)
    {
        $this->a = $a;
        $this->b = $b;
        $this->prime = $prime;
        $this->adapter = $adapter;
        $this->modAdapter = new ModularArithmetic($this->adapter, $prime);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\CurveFpInterface::getModAdapter()
     */
    public function getModAdapter()
    {
        return $this->modAdapter;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\CurveFpInterface::getInfinity()
     */
    public function getInfinity()
    {
        return new Point($this->adapter, $this, 0, 0, 0, true);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\CurveFpInterface::getPoint()
     */
    public function getPoint($x, $y, $order = null)
    {
        return new Point($this->adapter, $this, $x, $y, $order);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\CurveFpInterface::getGenerator()
     */
    public function getGenerator($x, $y, $order = null, RandomNumberGeneratorInterface $randomGenerator = null)
    {
        return new GeneratorPoint($this->adapter, $this, $x, $y, $order, $randomGenerator);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\CurveFpInterface::contains()
     */
    public function contains($x, $y)
    {
        $math = $this->adapter;

        $eq_zero = $math->cmp($math->mod($math->sub($math->pow($y, 2), $math->add($math->add($math->pow($x, 3), $math->mul($this->a, $x)), $this->b)), $this->prime), 0);

        return ($eq_zero == 0);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\CurveFpInterface::getA()
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\CurveFpInterface::getB()
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\CurveFpInterface::getPrime()
     */
    public function getPrime()
    {
        return $this->prime;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\CurveFpInterface::cmp()
     */
    public function cmp(CurveFpInterface $other)
    {
        $math = $this->adapter;

        $equal  = ($math->cmp($this->a, $other->getA()) == 0);
        $equal &= ($math->cmp($this->b, $other->getB()) == 0);
        $equal &= ($math->cmp($this->prime, $other->getPrime()) == 0);

        return ($equal) ? 0 : 1;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\CurveFpInterface::equals()
     */
    public function equals(CurveFpInterface $other)
    {
        return $this->cmp($other) == 0;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\CurveFpInterface::__toString()
     */
    public function __toString()
    {
        return 'curve('.$this->a.', '.$this->b.', '.$this->prime.')';
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'a' => (string) $this->a,
            'b' => (string) $this->b,
            'prime' => (string) $this->prime
        ];
    }
}
