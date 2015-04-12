<?php

namespace Mdanter\Ecc\Serializer\Curves;


use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Serializer\Util\HashAlgorithmOidMapper;

/**
 * Serialize a named curve to it's explicit parameters.
 */
class EcParamsExplicitSerializer
{
    const VERSION = 3;
    const HEADER = '-----BEGIN EC PARAMETERS-----';
    const FOOTER = '-----END EC PARAMETERS-----';

    /**
     * @param UncompressedPointSerializer $pointSerializer
     */
    public function __construct(UncompressedPointSerializer $pointSerializer)
    {
        $this->pointSerializer = $pointSerializer;
    }

    /**
     * @param NamedCurveFp $c
     * @param GeneratorPoint $G
     * @return string
     */
    public function serialize(NamedCurveFp $c, GeneratorPoint $G)
    {
        $math = $G->getAdapter();
        $hexSize = CurveOidMapper::getByteSize($c) * 2;

        $generatorHex = $this->pointSerializer->serialize($G);

        $domainParams = new Sequence(
            new Integer(self::VERSION), // version
            new Integer(1),                         // fieldId
            new Sequence(
                new OctetString(str_pad($math->decHex($c->getA()), $hexSize, '0', STR_PAD_LEFT)),
                new OctetString(str_pad($math->decHex($c->getA()), $hexSize, '0', STR_PAD_LEFT)),
                new BitString('')
            ),
            new BitString($generatorHex),
            new Integer($G->getOrder()),    // order
            new Integer(1),                          // cofactor
            HashAlgorithmOidMapper::getHashAlgorithmOid('sha1')
        );

        $payload = base64_encode($domainParams->getBinary());
        $content = self::HEADER . PHP_EOL
            . trim(chunk_split(base64_encode($payload), 64, PHP_EOL)).PHP_EOL
            . self::FOOTER;

        return $content;
    }

    /**
     * @param string $params
     * @return \Mdanter\Ecc\Curves\NamedCurveFp
     */
    public function parse($params)
    {
        $params = str_replace(self::HEADER, '', $params);
        $params = str_replace(self::FOOTER, '', $params);

        $oid = ObjectIdentifier::fromBinary(base64_decode($params));
        return CurveOidMapper::getCurveFromOid($oid);
    }
}