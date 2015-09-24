<?php

namespace Mdanter\Ecc\Primitives;


use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Util\Hasher;

class EcDomain
{
    /**
     * @var NamedCurveFp
     */
    private $curve;

    /**
     * @var GeneratorPoint
     */
    private $generator;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @var MathAdapterInterface
     */
    private $math;

    /**
     * @param MathAdapterInterface $math
     * @param NamedCurveFp $curve
     * @param GeneratorPoint $generatorPoint
     * @param Hasher $hasher - must be a known hash algorithm
     */
    public function __construct(MathAdapterInterface $math, NamedCurveFp $curve, GeneratorPoint $generatorPoint, Hasher $hasher)
    {
        if (!$curve->contains($generatorPoint->getX(), $generatorPoint->getY())) {
            throw new \RuntimeException('Provided generator point does not exist on curve');
        }

        $this->hasher = $hasher;
        $this->curve = $curve;
        $this->generator = $generatorPoint;
        $this->math = $math;
    }

    /**
     * @return NamedCurveFp
     */
    public function getCurve()
    {
        return $this->curve;
    }

    /**
     * @return GeneratorPoint
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * @return string
     */
    public function getSigAlgorithm()
    {
        return "ecdsa+" . $this->getHasher()->getAlgo();
    }

    /**
     * @return Hasher
     */
    public function getHasher()
    {
        return $this->hasher;
    }

    /**
     * @return Signer
     */
    public function getSigner()
    {
        return new Signer($this->math);
    }
}