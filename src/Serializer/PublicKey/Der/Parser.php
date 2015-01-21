<?php

namespace Mdanter\Ecc\Serializer\PublicKey\Der;

use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use PHPASN1\ASN_Object;
use PHPASN1\ASN_ObjectIdentifier;
use PHPASN1\ASN_Sequence;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\GeneratorPoint;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Point\PointSerializerInterface;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\PublicKey;

class Parser
{

    private $adapter;

    private $pointSerializer;

    public function __construct(MathAdapterInterface $adapter, PointSerializerInterface $pointSerializer = null)
    {
        $this->adapter = $adapter;
        $this->pointSerializer = $pointSerializer ?: new UncompressedPointSerializer($adapter);
    }

    public function parse($binaryData)
    {
        $asnObject = ASN_Object::fromBinary($binaryData);

        if (! ($asnObject instanceof ASN_Sequence) || $asnObject->getNumberofChildren() != 2) {
            throw new \RuntimeException('Invalid data.');
        }

        $children = $asnObject->getChildren();

        $oid = $children[0]->getChildren()[0];
        $curveOid = $children[0]->getChildren()[1];
        $encodedKey = $children[1];

        if ($oid->getContent() !== DerPublicKeySerializer::X509_ECDSA_OID) {
            throw new \RuntimeException('Invalid data: non X509 data.');
        }

        $generator = CurveOidMapper::getGeneratorFromOid($curveOid);

        return $this->parseKey($generator, $encodedKey->getContent());
    }

    public function parseKey(GeneratorPoint $generator, $data)
    {
        $point = $this->pointSerializer->unserialize($generator->getCurve(), $data);

        return new PublicKey($this->adapter, $generator, $point);
    }
}
