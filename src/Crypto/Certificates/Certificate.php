<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 09/07/15
 * Time: 02:07
 */

namespace Mdanter\Ecc\Crypto\Certificates;


use Mdanter\Ecc\Crypto\Signature\SignatureInterface;

class Certificate
{
    /**
     * @var CertificateInfo
     */
    private $info;

    /**
     * @var string
     */
    private $sigAlg;

    /**
     * @var SignatureInterface
     */
    private $signature;

    /**
     * @param CertificateInfo $info
     * @param string $sigAlg
     * @param SignatureInterface $signature
     */
    public function __construct(
        CertificateInfo $info,
        $sigAlg,
        SignatureInterface $signature
    ) {
        $this->info = $info;
        $this->sigAlg = $sigAlg;
        $this->signature = $signature;
    }

    /**
     * @return CertificateInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return string
     */
    public function getSigAlgorithm()
    {
        return $this->sigAlg;
    }

    /**
     * @return SignatureInterface
     */
    public function getSignature()
    {
        return $this->signature;
    }
}