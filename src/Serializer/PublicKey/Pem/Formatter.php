<?php

namespace Mdanter\Ecc\Serializer\PublicKey\Pem;

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
                new ASN_ObjectIdentifier(PemPublicKeySerializer::X509_ECDSA_OID),
                CurveOidMapper::getCurveOid($key->getCurve())
            ),
            new ASN_BitString($this->encodePoint($key->getPoint()))
        );
        
        return base64_encode($sequence->getBinary());
    }
    
    public function encodePoint(PointInterface $point)
    {
        $xLength = NumberSize::getCeiledByteSize($this->adapter, $point->getX()) + 1;
        $yLength = NumberSize::getCeiledByteSize($this->adapter, $point->getY()) + 1;
        
        $length = max($xLength, $yLength);
        
        $hexString = '04';
        $hexString .= str_pad($this->adapter->decHex($point->getX()), $length, '0');
        $hexString .= str_pad($this->adapter->decHex($point->getY()), $length, '0');
        
        return $hexString;
    }
}
