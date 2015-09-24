<?php

namespace Mdanter\Ecc\Serializer\Certificates;


use FG\ASN1\AbstractString;
use FG\ASN1\Object;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;
use FG\ASN1\Universal\UTCTime;
use Mdanter\Ecc\Crypto\Certificates\Certificate;
use Mdanter\Ecc\Crypto\Certificates\CertificateInfo;
use Mdanter\Ecc\Crypto\Certificates\CsrSubject;
use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
use Mdanter\Ecc\Serializer\Util\CertificateSubjectOidMapper;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Serializer\Util\SigAlgorithmOidMapper;

class CertificateSerializer
{
    const HEADER = '-----BEGIN CERTIFICATE-----';
    const FOOTER = '-----END CERTIFICATE-----';
    const UTCTIME_FORMAT = 'Y-m-d\tH:i:s';
    const ECPUBKEY_OID = '1.2.840.10045.2.1';

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
                new ObjectIdentifier(self::ECPUBKEY_OID),
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
            //new Integer($info->getVersion()),
            new Integer($info->getSerialNo()),
            new Sequence(
                SigAlgorithmOidMapper::getSigAlgorithmOid($info->getSigAlgo())
            ),
            $this->subjectSer->toAsn($info->getIssuerInfo()),
            new Sequence(
                new UTCTime($info->getValidityStart()->format(self::UTCTIME_FORMAT)),
                new UTCTime($info->getValidityEnd()->format(self::UTCTIME_FORMAT))
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
        $base64 = base64_encode($payload);
        $content = trim(chunk_split($base64, 64, PHP_EOL)).PHP_EOL;

        return self::HEADER . PHP_EOL
        . $content
        . self::FOOTER . PHP_EOL;
    }

    /**
     * @param Sequence $object
     * @return CsrSubject
     * @throws \Exception
     */
    public function parseSubject(Sequence $object)
    {
        $objChildren = $object->getChildren();

        $subjectArr = [];
        foreach ($objChildren as $child) {
            if (!$child instanceof Set) {
                throw new \InvalidArgumentException('not a set');
            }

            $set = $child->getChildren();
            if (count($set) !== 1) {
                throw new \InvalidArgumentException('Invalid data - Set');
            }

            $sequence = $set[0];
            $children = $sequence->getContent();

            if (!$children[0] instanceof ObjectIdentifier) {
                throw new \InvalidArgumentException('Invalid data - subject info key');
            }

            $key = CertificateSubjectOidMapper::getKeyFromOid($children[0]);

            if (!$children[1] instanceof AbstractString) {
                throw new \InvalidArgumentException('Invalid data - String');
            }
            $subjectArr[$key] = $children[1]->getContent();
        }

        return new CsrSubject($subjectArr);
    }

    /**
     * @param Sequence $object
     * @return \Mdanter\Ecc\Crypto\Key\PublicKey|PublicKeyInterface
     */
    public function parseSubjectKeyInfo(Sequence $object)
    {
        $pubkey = $this->pubKeySer->parse($object->getBinary());
        return $pubkey;
    }

    /**
     * @param Sequence $info
     * @return CertificateInfo
     */
    public function parseCertificateInfo(Sequence $info)
    {
        if ($info->getNumberOfChildren() < 6) {
            throw new \InvalidArgumentException('Invalid data');
        }

        $infoChildren = $info->getChildren();

        // Parse SerialNo
        if (!$infoChildren[0] instanceof Integer) {
            throw new \InvalidArgumentException('Invalid data - serialNo');
        }
        $serialNo = $infoChildren[0]->getContent();

        // Parse Signature Algorithm
        if (!$infoChildren[1] instanceof Sequence || $infoChildren[1]->getNumberOfChildren() < 1 || !$infoChildren[1]->getChildren()[0] instanceof ObjectIdentifier){
            throw new \InvalidArgumentException('Invalid data - sigalg');
        }
        $sigAlgSet = $infoChildren[1]->getChildren();
        /** @var ObjectIdentifier $sigAlgOid */
        $sigAlgOid = $sigAlgSet[0];
        $sigAlg = SigAlgorithmOidMapper::getKnownAlgorithmFromOid($sigAlgOid);

        // Parse Issuer
        if (!$infoChildren[2] instanceof Sequence) {
            throw new \InvalidArgumentException('Invalid data - Issuer');
        }
        $issuer = $this->parseSubject($infoChildren[2]);

        // Parse Validity - array of UTCTime
        $validity = $infoChildren[3];
        if (!$validity instanceof Sequence || $validity->getNumberOfChildren() !== 2) {
            throw new \InvalidArgumentException('Invalid Validity');
        }

        $validityContents = $validity->getChildren();
        if (!$validityContents[0] instanceof UTCTime || !$validityContents[1] instanceof UTCTime) {
            throw new \InvalidArgumentException('Invalid Validity');
        }

        /** @var UTCTime $validityEnd */
        /** @var UTCTime $validityStart */
        list ($validityStart, $validityEnd) = $validityContents;

        // Parse Subject
        if (!$infoChildren[4] instanceof Sequence) {
            throw new \InvalidArgumentException('Invalid data - Subject');
        }
        $subject = $this->parseSubject($infoChildren[4]);

        // Parse Subject Public Key Info
        if (!$infoChildren[5] instanceof Sequence) {
            throw new \InvalidArgumentException('Invalid data - Subject Public Key');
        }
        $subjectKey = $this->parseSubjectKeyInfo($infoChildren[5]);

        return new CertificateInfo(
            $serialNo,
            $sigAlg,
            $issuer,
            $subject,
            $subjectKey,
            $validityStart->getContent(),
            $validityEnd->getContent()
        );
    }

    /**
     * @param Sequence $sigSection
     * @return string
     * @throws \Exception
     */
    public function parseSigAlg(Sequence $sigSection)
    {
        $sigInfo = $sigSection->getContent();
        if (!$sigInfo[0] instanceof ObjectIdentifier) {
            throw new \Exception('Invaid sig: object identfier');
        }

        return SigAlgorithmOidMapper::getAlgorithmFromOid($sigInfo[0]);
    }

    /**
     * @param $certificate
     * @return Certificate
     * @throws \Exception
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function parse($certificate)
    {
        $certificate = str_replace(self::HEADER, '', $certificate);
        $certificate = str_replace(self::FOOTER, '', $certificate);

        $binary = base64_decode($certificate);

        $asnObject = Object::fromBinary($binary);
        if (!$asnObject instanceof Sequence || $asnObject->getNumberOfChildren() !== 3) {
            throw new \InvalidArgumentException('Invalid data.');
        }

        // Parse Certificate Info
        $children = $asnObject->getChildren();
        if (!$children[0] instanceof Sequence) {
            throw new \InvalidArgumentException('Invalid data: certificate info');
        }
        $info = $this->parseCertificateInfo($children[0]);

        // Parse Signature Algorithm
        $sigSection = $children[1];
        if (!$sigSection instanceof Sequence) {
            throw new \Exception('Invaid sig algo section');
        }
        $sigAlg = $this->parseSigAlg($sigSection);

        // Parse Signature
        if (!$children[2] instanceof BitString) {
            throw new \Exception('Invaid signature');
        }
        $signature = $this->sigSer->parse(hex2bin($children[2]->getContent()));

        return new Certificate(
            $info,
            $sigAlg,
            $signature
        );
    }
}