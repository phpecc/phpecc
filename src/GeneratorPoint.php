<?php

namespace Mdanter\Ecc;

/**
 * Curve point from which public and private keys can be derived.
 *
 * @author thibaud
 */
class GeneratorPoint extends Point
{

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

        if (! $point->equals(Points::infinity())) {
            return false;
        }

        return true;
    }

    public function createKeyExchange()
    {
        return new EcDH($this, $this->getAdapter());
    }

    public function createPrivateKey()
    {
        $secret = $this->getAdapter()->rand($this->getOrder());

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
