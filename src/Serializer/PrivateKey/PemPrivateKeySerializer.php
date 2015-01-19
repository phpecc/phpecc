<?php

namespace Mdanter\Ecc\Serializer\PrivateKey;

use PHPASN1\ASN_ObjectIdentifier;
use PHPASN1\ASN_Sequence;
use PHPASN1\ASN_Integer;
use PHPASN1\ASN_BitString;
use Mdanter\Ecc\PrivateKeyInterface;
use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\Math\MathAdapterFactory;
use PHPASN1\ASN_Object;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use PHPASN1\ASN_OctetString;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use PHPASN1\ASN_UnknownConstructedObject;
use Mdanter\Ecc\Util\NumberSize;
use Mdanter\Ecc\Serializer\Util\OctetStringConverter;
use Mdanter\Ecc\Serializer\Util\ASN\ASNContext;

/**
 * PEM Private key formatter 
 *  
 * @link https://tools.ietf.org/html/rfc5915
 */
class PemPrivateKeySerializer implements PrivateKeySerializerInterface
{
    
    const VERSION = 1;
    
    private $adapter;
    
    private $pubKeySerializer;
    
    public function __construct(MathAdapterInterface $adapter = null, PemPublicKeySerializer $pubKeySerializer = null)
    {
        $this->adapter = $adapter ?: MathAdapterFactory::getAdapter();
        $this->pubKeySerializer = $pubKeySerializer ?: new PemPublicKeySerializer($this->adapter);
    }
    
    public function serialize(PrivateKeyInterface $key)
    {
        $privateKeyInfo = new ASN_Sequence(
            new ASN_Integer(self::VERSION),
            new ASN_OctetString($this->formatKey($key)),
            new ASNContext(160, CurveOidMapper::getCurveOid($key->getPoint()->getCurve())),
            new ASNContext(161, $this->encodePubKey($key))
        );
        
        return base64_encode($privateKeyInfo->getBinary());
    }
    
    private function encodePubKey(PrivateKeyInterface $key)
    {
        return new ASN_BitString(
            $this->pubKeySerializer->getUncompressedKey($key->getPublicKey())
        );
    } 
    
    private function formatKey(PrivateKeyInterface $key)
    {        
        return $this->adapter->decHex($key->getSecret());
    }
    
    public function parse($formattedKey)
    {
        $data = base64_decode($formattedKey);
        $asnObject = ASN_Object::fromBinary($data);
        
        if (! ($asnObject instanceof ASN_Sequence) || $asnObject->getNumberofChildren() !== 4) {
            throw new \RuntimeException('Invalid data.');
        }
        
        $children = $asnObject->getChildren();
        
        $version = $children[0];
        $key = $this->adapter->hexDec($children[1]->getContent());
        $oid = $children[2]->getFirstChild();
        
        //var_dump($children[1], $children[2]);
        
        $generator = CurveOidMapper::getGeneratorFromOid($oid);
        
        return $generator->getPrivateKeyFrom($key);
    }
}
