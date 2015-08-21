<?php

namespace Mdanter\Ecc\Serializer\Curves;


use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Curves\CurveRandomSeed;
use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
//use Mdanter\Ecc\Serializer\Util\HashAlgorithmOidMapper;

/**
 * Serialize a named curve to it's explicit parameters.
 */
class EcParamsSerializer
{
    const VERSION = 3;
    const HEADER = '-----BEGIN EC PARAMETERS-----';
    const FOOTER = '-----END EC PARAMETERS-----';

    const FIELD_ID = '1.2.840.10045.1.1';

    /**
     * @param UncompressedPointSerializer $pointSerializer
     */
    public function __construct(UncompressedPointSerializer $pointSerializer)
    {
        $this->pointSerializer = $pointSerializer;
    }

    /**
     * @param NamedCurveFp $c
     * @return Sequence
     */
    private function getFieldIdAsn(MathAdapterInterface $math, NamedCurveFp $c)
    {
        return new Sequence(
            new ObjectIdentifier(self::FIELD_ID), // 1.2.840.10045.3.1.1.7
            new Integer($c->getPrime())
        );
    }

    /**
     * @param MathAdapterInterface $math
     * @param NamedCurveFp $c
     * @return Sequence
     */
    private function getCurveAsn(MathAdapterInterface $math, NamedCurveFp $c)
    {
        $a = new OctetString($math->decHex($math->mod($c->getA(), $c->getPrime())));
        $b = new OctetString($math->decHex($math->mod($c->getB(), $c->getPrime())));

        try {
            $seed = CurveRandomSeed::getSeed($c);
            return new Sequence(
                $a,
                $b,
                new BitString($seed)
            );
        } catch (\Exception $e) {
            return new Sequence(
                $a,
                $b
            );
        }
    }

    /**
     * @param NamedCurveFp $c
     * @param GeneratorPoint $G
     * @return string
     */
    public function serialize(NamedCurveFp $c, GeneratorPoint $G)
    {
        $math = $G->getAdapter();

        $fieldID = $this->getFieldIdAsn($math, $c);
        $curve = $this->getCurveAsn($math, $c);

        $domain = new Sequence(
            new Integer(1),
            $fieldID,
            $curve,
            new OctetString($this->pointSerializer->serialize($G)),
            new Integer($G->getOrder()),
            new Integer(1)
            // Hash function oid ?
        );

        $payload = $domain->getBinary();

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