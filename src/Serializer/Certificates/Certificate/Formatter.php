<?php

namespace Mdanter\Ecc\Serializer\Certificates\Certificate;

use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\UTCTime;
use Mdanter\Ecc\Crypto\Certificates\Certificate;
use Mdanter\Ecc\Crypto\Certificates\CertificateInfo;
use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\Certificates\CertificateSerializer;
use Mdanter\Ecc\Serializer\Certificates\CsrSubjectSerializer;
use Mdanter\Ecc\Serializer\Certificates\Extensions\AbstractExtensions;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Serializer\Util\SigAlgorithmOidMapper;

class Formatter
{
    private $extension;

    /**
     * @var CsrSubjectSerializer
     */
    private $subjectSer;

    /**
     * @var DerPublicKeySerializer
     */
    private $pubKeySer;

    /**
     * @var DerSignatureSerializer
     */
    private $sigSer;

    /**
     * @param CsrSubjectSerializer $csrSubSerializer
     * @param DerPublicKeySerializer $publicKeySerializer
     * @param DerSignatureSerializer $sigSer
     */
    public function __construct(CsrSubjectSerializer $csrSubSerializer, DerPublicKeySerializer $publicKeySerializer, DerSignatureSerializer $sigSer, AbstractExtensions $extension = null)
    {
        $this->subjectSer = $csrSubSerializer;
        $this->pubKeySer = $publicKeySerializer;
        $this->sigSer = $sigSer;
        $this->extension = $extension;
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
                new ObjectIdentifier(CertificateSerializer::ECPUBKEY_OID),
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
        if ($this->extension === null) {
            return new Sequence(
            //new Integer($info->getVersion()),
                new Integer($info->getSerialNo()),
                new Sequence(
                    SigAlgorithmOidMapper::getSigAlgorithmOid($info->getSigAlgo())
                ),
                $this->subjectSer->toAsn($info->getIssuerInfo()),
                new Sequence(
                    new UTCTime($info->getValidityStart()->format(CertificateSerializer::UTCTIME_FORMAT)),
                    new UTCTime($info->getValidityEnd()->format(CertificateSerializer::UTCTIME_FORMAT))
                ),
                $this->subjectSer->toAsn($info->getSubjectInfo()),
                $this->getSubjectKeyASN($curve, $info->getPublicKey())
            );
        }

        return new Sequence(
        //new Integer($info->getVersion()),
            new Integer($info->getSerialNo()),
            new Sequence(
                SigAlgorithmOidMapper::getSigAlgorithmOid($info->getSigAlgo())
            ),
            $this->subjectSer->toAsn($info->getIssuerInfo()),
            new Sequence(
                new UTCTime($info->getValidityStart()->format(CertificateSerializer::UTCTIME_FORMAT)),
                new UTCTime($info->getValidityEnd()->format(CertificateSerializer::UTCTIME_FORMAT))
            ),
            $this->subjectSer->toAsn($info->getSubjectInfo()),
            $this->getSubjectKeyASN($curve, $info->getPublicKey()),
            $this->extension->apply($info)
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


}