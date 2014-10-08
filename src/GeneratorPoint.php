<?php

namespace Mdanter\Ecc;

/**
 * Curve point from which public and private keys can be derived.
 *
 * @author thibaud
 *
 */
class GeneratorPoint implements PointInterface
{
    /**
     * Point instance to which core point features are delegated.
     *
     * @var PointInterface
     */
    private $point;

    /**
     * Math adapter used for calculations.
     *
     * @var MathAdapter
     */
    private $adapter;

    /**
     * Initialize a new instance using an existing curve point and a math adapter.
     *
     * @param PointInterface $wrapped
     * @param MathAdapter $adapter
     */
    public function __construct(PointInterface $wrapped, MathAdapter $adapter)
    {
        $this->point = $wrapped;
        $this->adapter = $adapter;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::add()
     */
    public function add(PointInterface $addend)
    {
        return new self($this->point->add($addend), $this->adapter);
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::cmp()
     */
    public function cmp(PointInterface $other)
    {
        return $this->point->cmp($other);
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::equals()
     */
    public function equals(PointInterface $other)
    {
        return $this->point->equals($other);
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::getCurve()
     */
    public function getCurve()
    {
        return $this->point->getCurve();
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::getDouble()
     */
    public function getDouble()
    {
        return new self($this->point->getDouble(), $this->adapter);
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::getOrder()
     */
    public function getOrder()
    {
        return $this->point->getOrder();
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::getX()
     */
    public function getX()
    {
        return $this->point->getX();
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::getY()
     */
    public function getY()
    {
        return $this->point->getY();
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PointInterface::mul()
     */
    public function mul($multiplier)
    {
        return new self($this->point->mul($multiplier), $this->adapter);
    }

    /**
     * Verifies validity of given coordinates against the current point and its point.
     *
     * @todo Check if really necessary here (only used for testing in lib)
     * @param int|string $x
     * @param int|string $y
     * @return boolean
     */
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

        $point = $curve->getPoint($x, $y)->mul($n);

        if (! $point->equals(Points::infinity())) {
            return false;
        }

        return true;
    }

    public function createKeyExchange()
    {
        return new EcDH($this, $this->adapter);
    }

    public function createPrivateKey()
    {
        $secret = $this->adapter->rand($this->getOrder());

        $pubPoint = $this->mul($secret);
        $pubKey = $this->getPublicKeyFrom($pubPoint->getX(), $pubPoint->getY(), $pubPoint->getOrder());

        return new PrivateKey($pubKey, $secret, $this->adapter);
    }

    public function getPublicKeyFrom($x, $y, $order = null)
    {
        $pubPoint = $this->getCurve()->getPoint($x, $y, $order);

        return new PublicKey($this, $pubPoint, $this->adapter);
    }

    public function getPrivateKeyFrom($secretMultiplier, $x, $y, $order = null)
    {
        return $this->getPublicKeyFrom($x, $y, $order)->getPrivateKey($secretMultiplier);
    }

    public function __toString()
    {
        return (string)$this->point;
    }
}
