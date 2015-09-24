<?php

namespace Mdanter\Ecc\Serializer\Util;


use FG\ASN1\Universal\ObjectIdentifier;

class HashAlgorithmOidMapper
{

    const SHA1_OID = '1.3.14.3.2.26.1';

    const SHA224_OID = '2.16.840.1.101.3.4.2.4';

    const SHA256_OID = '2.16.840.1.101.3.4.2.1';

    const SHA384_OID = '2.16.840.1.101.3.4.2.2';

    const SHA512_OID = '2.16.840.1.101.3.4.2.3';

    /**
     * @var array
     */
    private static $oidMap = array(
        'sha1' => self::SHA1_OID,
        'sha224' => self::SHA224_OID,
        'sha256' => self::SHA256_OID,
        'sha384' => self::SHA384_OID,
        'sha512' => self::SHA512_OID
    );

    /**
     * @var array
     */
    private static $sizeMap = array(
        'sha1' => 40,
        'sha224' => 56,
        'sha256' => 64,
        'sha384' => 96,
        'sha512' => 128
    );

    /**
     * @return array
     */
    public static function getNames()
    {
        return array_keys(self::$oidMap);
    }

    /**
     * @param string $hashAlgo
     * @return integer
     * @throws \RuntimeException
     */
    public static function getByteSize($hashAlgo)
    {
        if (array_key_exists($hashAlgo, self::$sizeMap)) {
            return self::$sizeMap[$hashAlgo];
        }

        throw new \RuntimeException('Unsupported hashing algorithm.');
    }

    /**
     * @param string $hashAlgo
     * @return ObjectIdentifier
     * @throws \RuntimeException
     */
    public static function getHashAlgorithmOid($hashAlgo)
    {
        if (array_key_exists($hashAlgo, self::$oidMap)) {
            $oidString = self::$oidMap[$hashAlgo];

            return new ObjectIdentifier($oidString);
        }

        throw new \RuntimeException('Unsupported hashing algorithm.');
    }

    /**
     * @param ObjectIdentifier $oid
     * @return callable
     * @throws \RuntimeException
     */
    public static function getHasherFromOid(ObjectIdentifier $oid)
    {
        $oidString = $oid->getContent();
        $invertedMap = array_flip(self::$oidMap);

        if (array_key_exists($oidString, $invertedMap)) {
            $hashAlgorithm = $invertedMap[$oidString];
            return function ($data, $asBinary = false) use ($hashAlgorithm) {
                return hash($hashAlgorithm, $data, $asBinary);
            };
        }

        throw new \RuntimeException('Unsupported hashing algorithm.');
    }
}
