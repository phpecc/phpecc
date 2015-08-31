<?php

namespace Mdanter\Ecc\Serializer\Certificates\Certificate;

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
use Mdanter\Ecc\Serializer\Certificates\CertificateSerializer;
use Mdanter\Ecc\Serializer\PublicKey\PublicKeySerializerInterface;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
use Mdanter\Ecc\Serializer\Util\CertificateSubjectOidMapper;
use Mdanter\Ecc\Serializer\Util\SigAlgorithmOidMapper;

class Parser
{
    /**
     * @var PublicKeySerializerInterface
     */
    private $pubKeySer;

    /**
     * @var DerSignatureSerializer
     */
    private $sigSer;

    /**
     * @param PublicKeySerializerInterface $publicKeySerializer
     */
    public function __construct(PublicKeySerializerInterface $publicKeySerializer, DerSignatureSerializer $sigSerializer)
    {
        $this->pubKeySer = $publicKeySerializer;
        $this->sigSer = $sigSerializer;
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
     * @return \Mdanter\Ecc\Crypto\Key\PublicKeyInterface
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
     * @param string $certificate
     * @return Certificate
     * @throws \Exception
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function parse($certificate)
    {
        $certificate = str_replace(CertificateSerializer::HEADER, '', $certificate);
        $certificate = str_replace(CertificateSerializer::FOOTER, '', $certificate);

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
            throw new \Exception('Invalid signature');
        }
        $signature = $this->sigSer->parse(hex2bin($children[2]->getContent()));

        return new Certificate(
            $info,
            $sigAlg,
            $signature
        );
    }
}