<?php

namespace Mdanter\Ecc;

use Mdanter\Ecc\Random\RandomGeneratorFactory;

/**
 * Curve point from which public and private keys can be derived.
 *
 */
class GeneratorPoint extends Point
{

    private $generator;

    public function __construct(MathAdapterInterface $adapter, CurveFpInterface $curve, $x, $y, $order = null,
                                RandomNumberGeneratorInterface $generator = null)
    {
        $this->generator = $generator ?: RandomGeneratorFactory::getRandomGenerator();

        parent::__construct($adapter, $curve, $x, $y, $order);
    }

    /**
     * Verifies validity of given coordinates against the current point and its point.
     *
     * @todo   Check if really necessary here (only used for testing in lib)
     * @param  int|string $x
     * @param  int|string $y
     * @return boolean
     */
    public function isValid($x, $y)
    {
        $math = $this->getAdapter();

        $n = $this->getOrder();
        $curve = $this->getCurve();

        if ($math->cmp($x, 0) < 0 || $math->cmp($n, $x) <= 0 || $math->cmp($y, 0) < 0 || $math->cmp($n, $y) <= 0) {
            return false;
        }

        if (! $curve->contains($x, $y)) {
            return false;
        }

        $point = $curve->getPoint($x, $y)->mul($n);

        if (! $point->isInfinity()) {
            return false;
        }

        return true;
    }

    public function createPrivateKey()
    {
        $secret = $this->generator->generate($this->getOrder());

        return new PrivateKey($this->getAdapter(), $this, $secret);
    }

    public function getPublicKeyFrom($x, $y, $order = null)
    {
        $pubPoint = $this->getCurve()->getPoint($x, $y, $order);

        return new PublicKey($this->getAdapter(), $this, $pubPoint);
    }

    public function getPrivateKeyFrom($secretMultiplier)
    {
        return new PrivateKey($this->getAdapter(), $this, $secretMultiplier);
    }
}
