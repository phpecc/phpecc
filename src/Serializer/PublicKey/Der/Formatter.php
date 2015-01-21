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

class Formatter
{

    private $adapter;

    public function __construct(MathAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
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
            new ASN_BitString($this->encodePoint($key))
        );

        return $sequence->getBinary();
    }

    public function encodePoint(PublicKeyInterface $key)
    {
        $length = CurveOidMapper::getByteSize($key->getCurve()) * 2;

        $point = $key->getPoint();

        //error_log('Detected length: ' . $length);
        //error_log('Unpadded:' . $this->adapter->decHex($point->getX()));
        //error_log('Unpadded len:' . strlen($this->adapter->decHex($point->getX())));
        //error_log('Padded: ' . str_pad($this->adapter->decHex($point->getX()), $length, '0', STR_PAD_LEFT));

        $hexString = '04';
        $hexString .= str_pad($this->adapter->decHex($point->getX()), $length, '0', STR_PAD_LEFT);
        $hexString .= str_pad($this->adapter->decHex($point->getY()), $length, '0', STR_PAD_LEFT);

        //error_log('Resulting length: ' . strlen($hexString));
        //error_log('Hex: ' . $hexString);

        return $hexString;
    }
}
