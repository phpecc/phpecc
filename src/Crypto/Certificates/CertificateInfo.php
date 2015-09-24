<?php

namespace Mdanter\Ecc\Crypto\Certificates;


use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;

class CertificateInfo
{
    /**
     * @var int
     */
    private $serialNo;

    /**
     * @var string
     */
    private $sigAlgo;

    /**
     * @var CsrSubject
     */
    private $issuer;

    /**
     * @var CsrSubject
     */
    private $subject;

    /**
     * @var PublicKeyInterface
     */
    private $publicKey;

    /**
     * @var \DateTime
     */
    private $validityStart;

    /**
     * @var \DateTime
     */
    private $validityEnd;

    /**
     * @param string $serialNo
     * @param string $sigAlgo
     * @param CsrSubject $issuer
     * @param CsrSubject $subject
     * @param PublicKeyInterface $publicKey
     * @param \DateTime $validityStart
     * @param \DateTime $validityEnd
     */
    public function __construct(
        $serialNo,
        $sigAlgo,
        CsrSubject $issuer,
        CsrSubject $subject,
        PublicKeyInterface $publicKey,
        \DateTime $validityStart,
        \DateTime $validityEnd
    ) {
        $this->serialNo = $serialNo;
        $this->sigAlgo = $sigAlgo;
        $this->issuer = $issuer;
        $this->subject = $subject;
        $this->publicKey = $publicKey;
        $this->validityStart = $validityStart;
        $this->validityEnd = $validityEnd;
    }

    public function getVersion()
    {
        // Implicit, so we need logic to work this out.
        return 1;
    }

    /**
     * @return int|string
     */
    public function getSerialNo()
    {
        return $this->serialNo;
    }

    /**
     * @return string
     */
    public function getSigAlgo()
    {
        return $this->sigAlgo;
    }

    /**
     * @return CsrSubject
     */
    public function getIssuerInfo()
    {
        return $this->issuer;
    }

    /**
     * @return CsrSubject
     */
    public function getSubjectInfo()
    {
        return $this->subject;
    }

    /**
     * @return PublicKeyInterface
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @return \DateTime
     */
    public function getValidityStart()
    {
        return $this->validityStart;
    }

    /**
     * @return \DateTime
     */
    public function getValidityEnd()
    {
        return $this->validityEnd;
    }
}