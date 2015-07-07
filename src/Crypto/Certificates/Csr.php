<?php

namespace Mdanter\Ecc\Crypto\Certificates;
use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Serializer\Util\SigAlgorithmOidMapper;


class Csr
{
    /**
     * @var PublicKeyInterface
     */
    private $publicKey;

    /**
     * @var string
     */
    private $sigAlgorithm;

    /**
     * @var SignatureInterface
     */
    private $signature;

    /**
     * @var
     */
    private $subject;

    /**
     * @param CertificateSubject $subject
     * @param string $sigAlgorithm
     * @param PublicKeyInterface $publicKey
     * @param SignatureInterface $signature
     */
    public function __construct(CertificateSubject $subject, $sigAlgorithm, PublicKeyInterface $publicKey, SignatureInterface $signature)
    {
        SigAlgorithmOidMapper::getSigAlgorithmOid($sigAlgorithm);
        $this->sigAlgorithm = $sigAlgorithm;
        $this->publicKey = $publicKey;
        $this->signature = $signature;
        $this->subject = $subject;
    }

    /**
     * @return PublicKeyInterface
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @return SignatureInterface
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    public function getSigAlgorithm()
    {
        return $this->sigAlgorithm;
    }
}