<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Util;

use FG\ASN1\Universal\ObjectIdentifier;
use Mdanter\Ecc\Curves\BrainpoolCurve;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Curves\SecgCurve;
use Mdanter\Ecc\Exception\UnsupportedCurveException;
use Mdanter\Ecc\Primitives\CurveFpInterface;
use Mdanter\Ecc\Primitives\GeneratorPoint;

class CurveOidMapper
{

    const NIST_P192_OID = '1.2.840.10045.3.1.1';

    const NIST_P224_OID = '1.3.132.0.33';

    const NIST_P256_OID = '1.2.840.10045.3.1.7';

    const NIST_P384_OID = '1.3.132.0.34';

    const NIST_P521_OID = '1.3.132.0.35';

    const BRAINPOOL_P160R1_OID = '1.3.36.3.3.2.8.1.1.1';
    const BRAINPOOL_P256R1_OID = '1.3.36.3.3.2.8.1.1.7';
    const BRAINPOOL_P384R1_OID = '1.3.36.3.3.2.8.1.1.11';
    const BRAINPOOL_P512R1_OID = '1.3.36.3.3.2.8.1.1.14';

    const SECP_112R1_OID = '1.3.132.0.6';

    const SECP_192K1_OID = '1.3.132.0.31';

    const SECP_256K1_OID = '1.3.132.0.10';

    const SECP_256R1_OID = '1.2.840.10045.3.1.7';

    const SECP_384R1_OID = '1.3.132.0.34';

    /**
     * @var array
     */
    private static $oidMap = array(
        NistCurve::NAME_P192 => self::NIST_P192_OID,
        NistCurve::NAME_P224 => self::NIST_P224_OID,
        NistCurve::NAME_P256 => self::NIST_P256_OID,
        NistCurve::NAME_P384 => self::NIST_P384_OID,
        NistCurve::NAME_P521 => self::NIST_P521_OID,
        BrainpoolCurve::NAME_P160R1 => self::BRAINPOOL_P160R1_OID,
        BrainpoolCurve::NAME_P256R1 => self::BRAINPOOL_P256R1_OID,
        BrainpoolCurve::NAME_P384R1 => self::BRAINPOOL_P384R1_OID,
        BrainpoolCurve::NAME_P512R1 => self::BRAINPOOL_P512R1_OID,
        SecgCurve::NAME_SECP_112R1 => self::SECP_112R1_OID,
        SecgCurve::NAME_SECP_192K1 => self::SECP_192K1_OID,
        SecgCurve::NAME_SECP_256K1 => self::SECP_256K1_OID,
        SecgCurve::NAME_SECP_256R1 => self::SECP_256R1_OID,
        SecgCurve::NAME_SECP_384R1 => self::SECP_384R1_OID,
    );

    /**
     * @var array
     */
    private static $sizeMap = array(
        NistCurve::NAME_P192 => 192/8,
        NistCurve::NAME_P224 => 224/8,
        NistCurve::NAME_P256 => 256/8,
        NistCurve::NAME_P384 => 384/8,
        NistCurve::NAME_P521 => 66,
        BrainpoolCurve::NAME_P160R1 => 160/8,
        BrainpoolCurve::NAME_P256R1 => 256/8,
        BrainpoolCurve::NAME_P330R1 => 42,
        BrainpoolCurve::NAME_P384R1 => 384/8,
        BrainpoolCurve::NAME_P512R1 => 512/8,
        SecgCurve::NAME_SECP_112R1 => 112/8,
        SecgCurve::NAME_SECP_192K1 => 192/8,
        SecgCurve::NAME_SECP_256K1 => 256/8,
        SecgCurve::NAME_SECP_256R1 => 256/8,
        SecgCurve::NAME_SECP_384R1 => 384/8,
    );

    /**
     * @return array
     */
    public static function getNames(): array
    {
        return array_keys(self::$oidMap);
    }

    /**
     * @param CurveFpInterface $curve
     * @return int
     */
    public static function getByteSize(CurveFpInterface $curve): int
    {
        if ($curve instanceof NamedCurveFp && array_key_exists($curve->getName(), self::$sizeMap)) {
            return self::$sizeMap[$curve->getName()];
        }

        throw new UnsupportedCurveException('Unsupported curve type');
    }

    /**
     * @param NamedCurveFp $curve
     * @return ObjectIdentifier
     */
    public static function getCurveOid(NamedCurveFp $curve): ObjectIdentifier
    {
        if (array_key_exists($curve->getName(), self::$oidMap)) {
            $oidString = self::$oidMap[$curve->getName()];

            return new ObjectIdentifier($oidString);
        }

        throw new UnsupportedCurveException('Unsupported curve type');
    }

    /**
     * @param ObjectIdentifier $oid
     * @return NamedCurveFp
     */
    public static function getCurveFromOid(ObjectIdentifier $oid): NamedCurveFp
    {
        $oidString = $oid->getContent();
        $invertedMap = array_flip(self::$oidMap);

        if (array_key_exists($oidString, $invertedMap)) {
            return CurveFactory::getCurveByName($invertedMap[$oidString]);
        }

        $error = new UnsupportedCurveException('Invalid data: unsupported curve.');
        $error->setOid($oidString);
        throw $error;
    }

    /**
     * @param ObjectIdentifier $oid
     * @return GeneratorPoint
     */
    public static function getGeneratorFromOid(ObjectIdentifier $oid): GeneratorPoint
    {
        $oidString = $oid->getContent();
        $invertedMap = array_flip(self::$oidMap);

        if (array_key_exists($oidString, $invertedMap)) {
            return CurveFactory::getGeneratorByName($invertedMap[$oidString]);
        }

        $error = new UnsupportedCurveException('Invalid data: unsupported generator.');
        $error->setOid($oidString);
        throw $error;
    }
}
