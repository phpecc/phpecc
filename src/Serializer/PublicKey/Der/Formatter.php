<?php

namespace Mdanter\Ecc\Serializer\PublicKey\Der;

use Mdanter\Ecc\PointInterface;
use Mdanter\Ecc\PublicKeyInterface;
use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use PHPASN1\ASN_Sequence;
use PHPASN1\ASN_ObjectIdentifier;
use PHPASN1\ASN_BitString;
use Mdanter\Ecc\Util\NumberSize;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Point\PointSerializerInterface;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;

class Formatter
{

    private $adapter;

    private $pointSerializer;

    public function __construct(MathAdapterInterface $adapter, PointSerializerInterface $pointSerializer = null)
    {
        $this->adapter = $adapter;
        $this->pointSerializer = $pointSerializer ?: new UncompressedPointSerializer($adapter);
    }

    public function format(PublicKeyInterface $key)
    {
        if (! ($key->getCurve() instanceof NamedCurveFp)) {
            throw new \RuntimeException('Not implemented for unnamed curves');
        }

        $sequence = new ASN_Sequence(
            new ASN_Sequence(
                new ASN_ObjectIdentifier(DerPublicKeySerializer::X509_ECDSA_OID),
                CurveOidMapper::getCurveOid($key->getCurve())
            ),
            new ASN_BitString($this->encodePoint($key->getPoint()))
        );

        return $sequence->getBinary();
    }

    public function encodePoint(PointInterface $point)
    {
        return $this->pointSerializer->serialize($point);
    }
}
