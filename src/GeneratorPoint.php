<?php

namespace Mdanter\Ecc;

class GeneratorPoint implements PointInterface
{
    private $point;

    private $adapter;

    public function __construct(PointInterface $wrapped, MathAdapter $adapter)
    {
        $this->point = $wrapped;
        $this->adapter = $adapter;
    }

    public function add(PointInterface $addend)
    {
        return new self($this->point->add($addend), $this->adapter);
    }

    public function cmp(PointInterface $other)
    {
        return $this->point->cmp($other);
    }

    public function equals(PointInterface $other)
    {
        return $this->point->equals($other);
    }

    public function getCurve()
    {
        return $this->point->getCurve();
    }

    public function getDouble()
    {
        return new self($this->point->getDouble(), $this->adapter);
    }

    public function getOrder()
    {
        return $this->point->getOrder();
    }

    public function getX()
    {
        return $this->point->getX();
    }

    public function getY()
    {
        return $this->point->getY();
    }

    public function mul($multiplier)
    {
        return new self($this->point->mul($multiplier), $this->adapter);
    }

    public function isValid($x, $y)
    {
        $math = $this->adapter;

        $n = $this->point->getOrder();
        $curve = $this->point->getCurve();

        if ($math->cmp($x, 0) < 0 || $math->cmp($n, $x) <= 0 || $math->cmp($y, 0) < 0 || $math->cmp($n, $y) <= 0) {
            return false;
        }

        if (! $curve->contains($x, $y)) {
            return false;
        }

        $point = $curve->getPoint($x, $y);
        $op = $point->mul($n);

        if (! $op->equals(Points::infinity())) {
            return false;
        }

        return true;
    }

    public function getPublicKey($x, $y, $order = null)
    {
        return new PublicKey($this->point, new Point($this->point->getCurve(), $x, $y, $order, $this->adapter), $this->adapter);
    }

    public function getPrivateKey($x, $y, $secretMultiplier)
    {
        return new PrivateKey($this->getPublicKey($x, $y), $secretMultiplier, $this->adapter);
    }

    public function __toString()
    {
        return (string) $this->point;
    }
}
