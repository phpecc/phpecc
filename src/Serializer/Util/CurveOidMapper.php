<?php

namespace Mdanter\Ecc\Serializer\Util;

use Mdanter\Ecc\Curves\NamedCurveFp;
use PHPASN1\ASN_ObjectIdentifier;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Curves\SecgCurve;

class CurveOidMapper
{
   
    const NIST_P192_OID = '1.2.840.10045.3.1.1';
    
    const NIST_P224_OID = '1.3.132.0.33';
    
    const NIST_P256_OID = '1.2.840.10045.3.1.7';
    
    const NIST_P384_OID = '1.3.132.0.34';
    
    const NIST_P521_OID = '1.3.132.0.35';
    
    const SECP_256K1_OID = '1.3.132.0.10';
    
    const SECP_256R1_OID = '1.2.840.10045.3.1.7';
    
    const SECP_384R1_OID = '1.3.132.0.34';

    private static $oidMap = array(
        NistCurve::NAME_P192 => self::NIST_P192_OID,
        NistCurve::NAME_P224 => self::NIST_P224_OID,
        NistCurve::NAME_P256 => self::NIST_P256_OID,
        NistCurve::NAME_P384 => self::NIST_P384_OID,
        NistCurve::NAME_P521 => self::NIST_P521_OID,
        SecgCurve::NAME_SECP_256K1 => self::SECP_256K1_OID,
        SecgCurve::NAME_SECP_384R1 => self::SECP_384R1_OID
    );
    
    public static function getNames()
    {
        return array_keys(self::$oidMap);
    }
    
    public static function getCurveOid(NamedCurveFp $curve)
    {
        if (array_key_exists($curve->getName(), self::$oidMap)) {
            $oidString = self::$oidMap[$curve->getName()];
            
            return new ASN_ObjectIdentifier($oidString);
        }
        
        throw new \RuntimeException('Unsupported curve type.');
    }

    public static function getCurveFromOid(ASN_ObjectIdentifier $oid)
    {
        $oidString = $oid->getContent();
        $invertedMap = array_flip(self::$oidMap);
    
        if (array_key_exists($oidString, $invertedMap)) {
            return CurveFactory::getGeneratorByName($invertedMap[$oidString]);
        }
    
        throw new \RuntimeException('Invalid data: unsupported curve.');
    }

    public static function getGeneratorFromOid(ASN_ObjectIdentifier $oid)
    {
        $oidString = $oid->getContent();
        $invertedMap = array_flip(self::$oidMap);
    
        if (array_key_exists($oidString, $invertedMap)) {
            return CurveFactory::getGeneratorByName($invertedMap[$oidString]);
        }
    
        throw new \RuntimeException('Invalid data: unsupported generator.');
    }
}
