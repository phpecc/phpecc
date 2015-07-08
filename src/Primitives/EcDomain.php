<?php

namespace Mdanter\Ecc\Primitives;


use Mdanter\Ecc\Crypto\Certificates\CsrSubject;
use Mdanter\Ecc\Crypto\Certificates\Csr;
use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\Certificates\CsrSubjectSerializer;
use Mdanter\Ecc\Serializer\Certificates\CsrSerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
use Mdanter\Ecc\Serializer\Util\HashAlgorithmOidMapper;

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
     * @var string
     */
    private $hashAlgo;

    /**
     * @var MathAdapterInterface
     */
    private $math;

    /**
     * @param MathAdapterInterface $math
     * @param NamedCurveFp $curve
     * @param GeneratorPoint $generatorPoint
     * @param string $hashAlgo - must be a known hash algorithm
     */
    public function __construct(MathAdapterInterface $math, NamedCurveFp $curve, GeneratorPoint $generatorPoint, $hashAlgo)
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

    /**
     * @param CsrSubject $subject
     * @param PrivateKeyInterface $privateKey
     * @return Csr
     */
    public function getCsr(CsrSubject $subject, PrivateKeyInterface $privateKey)
    {
        $publicKey = $privateKey->getPublicKey();
        $g = $this->getGenerator();
        $signer = $this->getSigner();
        $hasher = $this->getHasher();

        $serializer = new CsrSerializer(new CsrSubjectSerializer(), new DerPublicKeySerializer($this->math), new DerSignatureSerializer());
        $data = $serializer->getCertRequestInfoASN($this->curve, $publicKey, $subject)->getBinary();
        $hash = $this->math->hexDec($hasher($data, false));

        $rng = RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $data, $this->getHashAlgo());
        $k = $rng->generate($g->getOrder());
        $signature = $signer->sign($privateKey, $hash, $k);

        return new Csr(
            $subject,
            "ecdsa+" . $this->getHashAlgo(),
            $this->curve,
            $publicKey,
            $signature
        );
    }
}