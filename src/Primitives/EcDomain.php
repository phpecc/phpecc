<?php

namespace Mdanter\Ecc\Primitives;


use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Serializer\Util\HashAlgorithmOidMapper;

class EcDomain
{
    /**
     * @var CurveFp
     */
    private $curve;

    /**
     * @var GeneratorPoint
     */
    private $generator;

    /**
     * @var string
     */
    private $hashAlgo;

    /**
     * @var MathAdapterInterface
     */
    private $math;

    /**
     * @param MathAdapterInterface $math
     * @param CurveFp $curve
     * @param GeneratorPoint $generatorPoint
     * @param string $hashAlgo - must be a known hash algorithm
     */
    public function __construct(MathAdapterInterface $math, CurveFp $curve, GeneratorPoint $generatorPoint, $hashAlgo)
    {
        HashAlgorithmOidMapper::getHashAlgorithmOid($hashAlgo);
        if (!$curve->contains($generatorPoint->getX(), $generatorPoint->getY())) {
            throw new \RuntimeException('Provided generator point does not exist on curve');
        }

        $this->curve = $curve;
        $this->generator = $generatorPoint;
        $this->hashAlgo = $hashAlgo;
        $this->math = $math;
    }

    /**
     * @return CurveFp
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
    public function getHashAlgo()
    {
        return $this->hashAlgo;
    }

    /**
     * @return \FG\ASN1\Universal\ObjectIdentifier
     */
    public function getHashAlgoOid()
    {
        return HashAlgorithmOidMapper::getHashAlgorithmOid($this->hashAlgo);
    }

    /**
     * @return callable
     */
    public function getHasher()
    {
        return HashAlgorithmOidMapper::getHasherFromOid($this->getHashAlgoOid());
    }

    /**
     * @return Signer
     */
    public function getSigner()
    {
        return new Signer($this->math);
    }
}