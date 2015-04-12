<?php

namespace Mdanter\Ecc\Serializer\Curves;


use FG\ASN1\Universal\ObjectIdentifier;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;

/**
 * This class serializes the curve to its OID. For explicit domain parameters see
 * EcParamsExplicitSerializer.
 */
class EcParamsSerializer
{
    const HEADER = '-----BEGIN EC PARAMETERS-----';
    const FOOTER = '-----END EC PARAMETERS-----';

    /**
     * @param NamedCurveFp $c
     * @return string
     */
    public function serialize(NamedCurveFp $c)
    {
        $oid = CurveOidMapper::getCurveOid($c);

        $content = self::HEADER . PHP_EOL .
            base64_encode($oid->getBinary()) . PHP_EOL .
            self::FOOTER;

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