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
    
    private $derSerializer;
    
    public function __construct(DerPrivateKeySerializer $derSerializer)
    {
        $this->derSerializer = $derSerializer;
    }
    
    public function serialize(PrivateKeyInterface $key)
    {
        $privateKeyInfo = $this->derSerializer->serialize($key);
        
        $content  = '-----BEGIN EC PRIVATE KEY-----' . PHP_EOL;
        $content .= trim(chunk_split(base64_encode($privateKeyInfo), 64, PHP_EOL)) . PHP_EOL;
        $content .= '-----END EC PRIVATE KEY-----';
        
        return $content;
    }
    
    public function parse($formattedKey)
    {
        $formattedKey = str_replace('-----BEGIN EC PRIVATE KEY-----', '', $formattedKey);
        $formattedKey = str_replace('-----END EC PRIVATE KEY-----', '', $formattedKey);
        
        $data = base64_decode($formattedKey);
        
        return $this->derSerializer->parse($data);
    }
}
