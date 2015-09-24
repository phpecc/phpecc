<?php

namespace Mdanter\Ecc\Crypto\Certificates;

use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Serializer\Util\SigAlgorithmOidMapper;


class Csr
{
    /**
     * @var NamedCurveFp
     */
    private $curve;

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
     * @var CsrSubject
     */
    private $subject;

    /**
     * @param CsrSubject $subject
     * @param string $sigAlgorithm
     * @param NamedCurveFp $curve
     * @param PublicKeyInterface $publicKey
     * @param SignatureInterface $signature
     */
    public function __construct(CsrSubject $subject, $sigAlgorithm, NamedCurveFp $curve, PublicKeyInterface $publicKey, SignatureInterface $signature)
    {
        SigAlgorithmOidMapper::getSigAlgorithmOid($sigAlgorithm);
        $this->sigAlgorithm = $sigAlgorithm;
        $this->curve = $curve;
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

    /**
     * @return NamedCurveFp
     */
    public function getCurve()
    {
        return $this->curve;
    }

    /**
     * @return CsrSubject
     */
    public function getSubject()
    {
        return $this->subject;
    }
}