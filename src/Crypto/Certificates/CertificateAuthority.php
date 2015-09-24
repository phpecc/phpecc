<?php

namespace Mdanter\Ecc\Crypto\Certificates;


use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Primitives\EcDomain;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\Certificates\CertificateSerializer;
use Mdanter\Ecc\Serializer\Certificates\CsrSubjectSerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

class CertificateAuthority
{
    /**
     * @var MathAdapterInterface
     */
    private $math;

    /**
     * @var CsrSubject
     */
    private $issuer;

    /**
     * @var string
     */
    private $sigAlg;

    /**
     * @param MathAdapterInterface $math
     * @param CsrSubject $issuer
     * @param string $sigAlg
     */
    public function __construct(MathAdapterInterface $math, CsrSubject $issuer, $sigAlg)
    {
        $this->math = $math;
        $this->issuer = $issuer;
        $this->sigAlg = $sigAlg;
    }

    /**
     * @param Csr $csr
     * @param int $serialNumber
     * @param \DateTime $validityStart
     * @param \DateTime $validityEnd
     * @return CertificateInfo
     */
    public function createCertificateInfo(Csr $csr, $serialNumber, \DateTime $validityStart, \DateTime $validityEnd)
    {
        return new CertificateInfo(
            $serialNumber,
            $this->sigAlg, // or csr
            $this->issuer,
            $csr->getSubject(),
            $csr->getPublicKey(),
            $validityStart,
            $validityEnd
        );
    }

    /**
     * @param Csr $csr
     * @param EcDomain $domain
     * @param PrivateKeyInterface $privateKey
     * @param $serialNumber
     * @param \DateTime $validityStart
     * @param \DateTime $validityEnd
     * @return Certificate
     */
    public function createCertificate(Csr $csr, EcDomain $domain, PrivateKeyInterface $privateKey, $serialNumber, \DateTime $validityStart, \DateTime $validityEnd)
    {
        $generator = $domain->getGenerator();
        $signer = $domain->getSigner();
        $hasher = $domain->getHasher();

        $info = $this->createCertificateInfo($csr, $serialNumber, $validityStart, $validityEnd);

        $serializer = new CertificateSerializer(new CsrSubjectSerializer(), new DerPublicKeySerializer(), new DerSignatureSerializer());
        $dataHex = $serializer->signData($info);
        $hash = $hasher->hashDec($dataHex);

        $rng = RandomGeneratorFactory::getUrandomGenerator();
        $k = $rng->generate($generator->getOrder());
        $signature = $signer->sign($privateKey, $hash, $k);

        return new Certificate(
            $info,
            $domain->getSigAlgorithm(),
            $signature
        );
    }
}