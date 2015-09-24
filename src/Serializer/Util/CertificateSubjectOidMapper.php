<?php

namespace Mdanter\Ecc\Serializer\Util;


use FG\ASN1\Universal\ObjectIdentifier;

class CertificateSubjectOidMapper
{
    const COUNTRY_OID = '2.5.4.6';
    const LOCALITY_OID = '2.5.4.7';
    const STATE_OID = '2.5.4.8';
    const ORGANIZATION_OID = '2.5.4.10';
    const ORGANIZATIONUNIT_OID = '2.5.4.11';
    const COMMONNAME_OID = '2.5.4.3';
    const EMAILADDRESS_OID = '1.2.840.113549.1.9.1';

    private static $oidMap = [
        'country' => self::COUNTRY_OID,
        'state' => self::STATE_OID,
        'locality' => self::LOCALITY_OID,
        'organization' => self::ORGANIZATION_OID,
        'organizationUnit' => self::ORGANIZATIONUNIT_OID,
        'commonName' => self::COMMONNAME_OID,
        'emailAddress' => self::EMAILADDRESS_OID
    ];

    /**
     * @param $string
     * @return ObjectIdentifier
     * @throws \Exception
     */
    public static function getKeyOid($string)
    {
        if (array_key_exists($string, self::$oidMap)) {
            return new ObjectIdentifier(self::$oidMap[$string]);
        }

        throw new \Exception('Unknown Certificate Subject Key');
    }

    /**
     * @param ObjectIdentifier $oid
     * @return mixed
     * @throws \Exception
     */
    public static function getKeyFromOid(ObjectIdentifier $oid)
    {
        $content = $oid->getContent();
        $invertedMap = array_flip(self::$oidMap);

        if (array_key_exists($content, $invertedMap)) {
            return $invertedMap[$content];
        }

        throw new \Exception('Unknown Certificate Subject Key Oid');
    }
}