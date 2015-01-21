<?php

namespace Mdanter\Ecc\Serializer\PublicKey;

use Mdanter\Ecc\PublicKeyInterface;
use PHPASN1\ASN_Sequence;
use PHPASN1\ASN_Integer;
use Mdanter\Ecc\Curves\NamedCurveFp;
use PHPASN1\ASN_ObjectIdentifier;
use PHPASN1\ASN_BitString;
use PHPASN1\ASN_Object;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Curves\SecgCurve;
use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\Math\MathAdapterFactory;
use Mdanter\Ecc\PointInterface;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\GeneratorPoint;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Serializer\PublicKey\Pem\Formatter;
use Mdanter\Ecc\Serializer\PublicKey\Pem\Parser;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;

/**
 *
 * @link https://tools.ietf.org/html/rfc5480#page-3
 */
class PemPublicKeySerializer implements PublicKeySerializerInterface
{

    private $derSerializer;

    /**
     *
     * @param MathAdapterInterface $adapter
     */
    public function __construct(DerPublicKeySerializer $serializer)
    {
        $this->derSerializer = $serializer;
    }

    /**
     *
     * @param PublicKeyInterface $key
     * @return string
     */
    public function serialize(PublicKeyInterface $key)
    {
        $publicKeyInfo = $this->derSerializer->serialize($key);

        $content  = '-----BEGIN PUBLIC KEY-----' . PHP_EOL;
        $content .= trim(chunk_split(base64_encode($publicKeyInfo), 64, PHP_EOL)) . PHP_EOL;
        $content .= '-----END PUBLIC KEY-----';

        return $content;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\Serializer\PublicKey\PublicKeySerializerInterface::parse()
     */
    public function parse($formattedKey)
    {
        $formattedKey = str_replace('-----BEGIN PUBLIC KEY-----', '', $formattedKey);
        $formattedKey = str_replace('-----END PUBLIC KEY-----', '', $formattedKey);

        $data = base64_decode($formattedKey);

        return $this->derSerializer->parse($data);
    }
}
