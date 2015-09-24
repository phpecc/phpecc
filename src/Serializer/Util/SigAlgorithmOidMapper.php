<?php

namespace Mdanter\Ecc\Serializer\Util;


use FG\ASN1\Universal\ObjectIdentifier;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Util\Hasher;

class SigAlgorithmOidMapper
{
    const ECDSA_WITH_SHA1_OID = '1.2.840.10045.1';
    const ECDSA_WITH_SHA224_OID = '1.2.840.10045.4.3.1';
    const ECDSA_WITH_SHA256_OID = '1.2.840.10045.4.3.2';
    const ECDSA_WITH_SHA384_OID = '1.2.840.10045.4.3.3';
    const ECDSA_WITH_SHA512_OID = '1.2.840.10045.4.3.4';

    private static $oidMap = [
        'ecdsa+sha1' => self::ECDSA_WITH_SHA1_OID,
        'ecdsa+sha224' => self::ECDSA_WITH_SHA224_OID,
        'ecdsa+sha256' => self::ECDSA_WITH_SHA256_OID,
        'ecdsa+sha384' => self::ECDSA_WITH_SHA384_OID,
        'ecdsa+sha512' => self::ECDSA_WITH_SHA512_OID
    ];

    const RSA_WITH_SHA1_OID = '1.2.840.113549.1.1.5';

    private static $otherOidMap = [
        'rsa+sha1' => self::RSA_WITH_SHA1_OID
    ];

    /**
     * @return array
     */
    public static function getNames()
    {
        return array_keys(self::$oidMap);
    }

    /**
     * @param $sigAlgo
     * @return ObjectIdentifier
     */
    public static function getSigAlgorithmOid($sigAlgo)
    {
        if (array_key_exists($sigAlgo, self::$oidMap)) {
            $oidString = self::$oidMap[$sigAlgo];

            return new ObjectIdentifier($oidString);
        }

        throw new \RuntimeException('Unsupported signature algorithm.');
    }

    /**
     * @param string $sigAlgo
     * @return Hasher
     */
    public static function getHasher($sigAlgo)
    {
        if (array_key_exists($sigAlgo, self::$oidMap)) {
            $algo = explode("+", $sigAlgo)[1];
            return new Hasher(EccFactory::getAdapter(), $algo);
        }

        throw new \RuntimeException('Unsupported hashing algorithm.');
    }

    /**
     * @param ObjectIdentifier $oid
     * @return \Closure
     */
    public static function getAlgorithmFromOid(ObjectIdentifier $oid)
    {
        $oidString = $oid->getContent();
        $invertedMap = array_flip(self::$oidMap);

        if (array_key_exists($oidString, $invertedMap)) {
            $algorithm = $invertedMap[$oidString];
            return $algorithm;
        }

        throw new \RuntimeException('Unsupported hashing algorithm.');
    }

    /**
     * @param ObjectIdentifier $oid
     * @return mixed
     */
    public static function getKnownAlgorithmFromOid(ObjectIdentifier $oid)
    {
        $oidString = $oid->getContent();
        $supported = array_flip(self::$oidMap);
        $known = array_flip(self::$otherOidMap);
        $all = array_merge($supported, $known);

        if (array_key_exists($oidString, $all)) {
            return $all[$oidString];
        }

        throw new \RuntimeException('Unsupported signature algorithm.');
    }
}