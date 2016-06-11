<?php

namespace Mdanter\Ecc\Primitives;

use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Crypto\Key\PrivateKey;
use Mdanter\Ecc\Crypto\Key\PublicKey;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Random\RandomNumberGeneratorInterface;

/**
 * Curve point from which public and private keys can be derived.
 */
class GeneratorPoint extends Point
{
    /**
     * @var \Mdanter\Ecc\Random\DebugDecorator|RandomNumberGeneratorInterface|null
     */
    private $generator;

    /**
     * @param GmpMathInterface               $adapter
     * @param CurveFpInterface               $curve
     * @param \GMP                           $x
     * @param \GMP                           $y
     * @param \GMP                           $order
     * @param RandomNumberGeneratorInterface $generator
     */
    public function __construct(
        GmpMathInterface $adapter,
        CurveFpInterface $curve,
        $x,
        $y,
        $order = null,
        RandomNumberGeneratorInterface $generator = null
    ) {
        if (!GmpMath::checkGmpValue($x)) {
            throw new \InvalidArgumentException('Invalid argument #3 to GeneratorPoint constructor - must pass GMP resource or \GMP instance');
        }

        if (!GmpMath::checkGmpValue($y)) {
            throw new \InvalidArgumentException('Invalid argument #4 to GeneratorPoint constructor - must pass GMP resource or \GMP instance');
        }

        if (!is_null($order) && !GmpMath::checkGmpValue($order)) {
            throw new \InvalidArgumentException('Invalid argument #5 to GeneratorPoint constructor - must pass GMP resource or \GMP instance');
        }

        $this->generator = $generator ?: RandomGeneratorFactory::getRandomGenerator();
        parent::__construct($adapter, $curve, $x, $y, $order);
    }

    /**
     * Verifies validity of given coordinates against the current point and its point.
     *
     * @todo   Check if really necessary here (only used for testing in lib)
     * @param  resource|\GMP $x
     * @param  resource|\GMP $y
     * @return boolean
     */
    public function isValid($x, $y)
    {
        if (!GmpMath::checkGmpValue($x)) {
            throw new \InvalidArgumentException('Invalid argument #1 to GeneratorPoint::isValid - must pass GMP resource or \GMP instance');
        }

        if (!GmpMath::checkGmpValue($y)) {
            throw new \InvalidArgumentException('Invalid argument #2 to GeneratorPoint::isValid - must pass GMP resource or \GMP instance');
        }

        $math = $this->getAdapter();

        $n = $this->getOrder();
        $zero = gmp_init(0, 10);
        $curve = $this->getCurve();

        if ($math->cmp($x, $zero) < 0 || $math->cmp($n, $x) <= 0 || $math->cmp($y, $zero) < 0 || $math->cmp($n, $y) <= 0) {
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

    /**
     * @return PrivateKey
     */
    public function createPrivateKey()
    {
        $secret = $this->generator->generate($this->getOrder());

        return new PrivateKey($this->getAdapter(), $this, $secret);
    }

    /**
     * @param resource|\GMP $x
     * @param resource|\GMP $y
     * @param resource|\GMP $order
     * @return PublicKey
     */
    public function getPublicKeyFrom($x, $y, $order = null)
    {
        if (!GmpMath::checkGmpValue($x)) {
            throw new \InvalidArgumentException('Invalid argument #1 to GeneratorPoint::getPublicKeyFrom - must pass GMP resource or \GMP instance');
        }

        if (!GmpMath::checkGmpValue($y)) {
            throw new \InvalidArgumentException('Invalid argument #2 to GeneratorPoint::getPublicKeyFrom constructor - must pass GMP resource or \GMP instance');
        }

        if ($order !== null && GmpMath::checkGmpValue($order)) {
            throw new \InvalidArgumentException('Invalid argument #3 to GeneratorPoint::getPublicKeyFrom constructor - must pass GMP resource or \GMP instance');
        }

        $pubPoint = $this->getCurve()->getPoint($x, $y, $order);
        return new PublicKey($this->getAdapter(), $this, $pubPoint);
    }

    /**
     * @param resource|\GMP $secretMultiplier
     * @return PrivateKey
     */
    public function getPrivateKeyFrom($secretMultiplier)
    {
        if (!GmpMath::checkGmpValue($secretMultiplier)) {
            throw new \InvalidArgumentException('Invalid argument #1 to GeneratorPoint::getPrivateKeyFrom - must pass GMP resource or \GMP instance');
        }

        return new PrivateKey($this->getAdapter(), $this, $secretMultiplier);
    }
}
