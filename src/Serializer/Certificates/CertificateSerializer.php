<?php

namespace Mdanter\Ecc\Serializer\Certificates;


use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\UTCTime;
use FG\X509\CSR\Attributes;
use Mdanter\Ecc\Crypto\Certificates\Certificate;
use Mdanter\Ecc\Crypto\Certificates\CertificateInfo;
use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Serializer\Util\SigAlgorithmOidMapper;

class CertificateSerializer
{
    const HEADER = '-----BEGIN CERTIFICATE-----';
    const FOOTER = '-----END CERTIFICATE-----';

    /**
     * @var DerPublicKeySerializer
     */
    private $pubKeySer;

    /**
     * @var CsrSubjectSerializer
     */
    private $subjectSer;

    /**
     * @var DerSignatureSerializer
     */
    private $sigSer;

    /**
     * @param CsrSubjectSerializer $subSerializer
     * @param DerPublicKeySerializer $pubKeySerializer
     * @param DerSignatureSerializer $sigSerializer
     */
    public function __construct(CsrSubjectSerializer $subSerializer, DerPublicKeySerializer $pubKeySerializer, DerSignatureSerializer $sigSerializer)
    {
        $this->subjectSer = $subSerializer;
        $this->pubKeySer = $pubKeySerializer;
        $this->sigSer = $sigSerializer;
    }

    /**
     * @param NamedCurveFp $curve
     * @param PublicKeyInterface $publicKey
     * @return Sequence
     */
    public function getSubjectKeyASN(NamedCurveFp $curve, PublicKeyInterface $publicKey)
    {
        return new Sequence(
            new Sequence(
                new ObjectIdentifier('1.2.840.10045.2.1'),
                CurveOidMapper::getCurveOid($curve)
            ),
            new BitString($this->pubKeySer->getUncompressedKey($publicKey))
        );
    }

    /**
     * @param CertificateInfo $info
     * @return Sequence
     */
    public function getCertInfoAsn(CertificateInfo $info)
    {
        $curve = EccFactory::getSecgCurves()->curve256k1();

        return new Sequence(
            new Integer($info->getVersion()),
            new Integer($info->getSerialNo()),
            $this->subjectSer->toAsn($info->getIssuerInfo()),
            new Sequence(
                new UTCTime($info->getValidityStart()),
                new UTCTime($info->getValidityEnd())
            ),
            $this->subjectSer->toAsn($info->getSubjectInfo()),
            $this->getSubjectKeyASN($curve, $info->getPublicKey())
        );
    }

    /**
     * @param Certificate $cert
     * @return Sequence
     */
    public function getCertificateASN(Certificate $cert)
    {
        return new Sequence(
            $this->getCertInfoASN($cert->getInfo()),
            new Sequence(
                SigAlgorithmOidMapper::getSigAlgorithmOid($cert->getSigAlgorithm())
            ),
            new BitString(bin2hex($this->sigSer->serialize($cert->getSignature())))
        );
    }

    /**
     * @param Certificate $certificate
     * @return string
     */
    public function serialize(Certificate $certificate)
    {
        $payload = $this->getCertificateASN($certificate)->getBinary();
        $content = trim(chunk_split(base64_encode($payload), 64, PHP_EOL)).PHP_EOL;

        return self::HEADER . PHP_EOL
        . $content
        . self::FOOTER . PHP_EOL;
    }

    // TODO
    public function parse()
    {

    }
}