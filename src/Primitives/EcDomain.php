<?php

namespace Mdanter\Ecc\Primitives;


use Mdanter\Ecc\Crypto\Certificates\CertificateAuthority;
use Mdanter\Ecc\Crypto\Certificates\Csr;
use Mdanter\Ecc\Crypto\Certificates\CsrSubject;
use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\Certificates\CsrSerializer;
use Mdanter\Ecc\Serializer\Certificates\CsrSubjectSerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
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
        $hash = $hasher->hash($data);

        $rng = RandomGeneratorFactory::getRandomGenerator();
        $k = $rng->generate($g->getOrder());

        $hashDec = $this->math->hexDec($hash);
        $signature = $signer->sign($privateKey, $hashDec, $k);

        return new Csr(
            $subject,
            $this->getSigAlgorithm(),
            $this->curve,
            $publicKey,
            $signature
        );
    }

    /**
     * @param CsrSubject $issuer
     * @return CertificateAuthority
     */
    public function getCertAuthority(CsrSubject $issuer)
    {
        return new CertificateAuthority(
            $this->math,
            $issuer,
            $this->getSigAlgorithm()
        );
    }
}