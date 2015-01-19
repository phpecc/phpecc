<?php

namespace Mdanter\Ecc\Serializer\PublicKey\Pem;

use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use PHPASN1\ASN_Object;
use PHPASN1\ASN_ObjectIdentifier;
use PHPASN1\ASN_Sequence;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\GeneratorPoint;

class Parser
{
    
    private $adapter;
    
    public function __construct(MathAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function parse($string)
    {
        $binaryData = base64_decode($string);
        $asnObject = ASN_Object::fromBinary($binaryData);
        
        if (! ($asnObject instanceof ASN_Sequence) || $asnObject->getNumberofChildren() != 2) {
            throw new \RuntimeException('Invalid data.');
        }
        
        $children = $asnObject->getChildren();
        
        $oid = $children[0]->getChildren()[0];
        $curveOid = $children[0]->getChildren()[1];
        $encodedKey = $children[1];
        
        if ($oid->getContent() !== PemPublicKeySerializer::X509_ECDSA_OID) {
            throw new \RuntimeException('Invalid data: non X509 data.');
        }
        
        $generator = CurveOidMapper::getGeneratorFromOid($curveOid);
        
        return $this->parseKey($generator, $encodedKey->getContent());
    }

    private function parseKey(GeneratorPoint $generator, $data)
    {
        if (substr($data, 0, 2) != '04') {
            throw new \InvalidArgumentException('Invalid data: only uncompressed keys are supported.');
        }
        
        $data = substr($data, 2);
        $dataLength = strlen($data);
        
        $x = $this->adapter->hexDec(substr($data, 0, $dataLength / 2));
        $y = $this->adapter->hexDec(substr($data, $dataLength / 2));
        
        return $generator->getPublicKeyFrom($x, $y);
    }
}
