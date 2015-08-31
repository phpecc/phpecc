<?php

namespace Mdanter\Ecc\Tests\Serializer\Util;


use FG\ASN1\Universal\ObjectIdentifier;
use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Tests\AbstractTestCase;
use Mdanter\Ecc\Serializer\Util\HashAlgorithmOidMapper;

class HashAlgorithmOidMapperTest extends AbstractTestCase
{
    public function testGetNames()
    {
        $names = array(
            'sha1',
            'sha224',
            'sha256',
            'sha384',
            'sha512'
        );

        $this->assertEquals($names, HashAlgorithmOidMapper::getNames());
    }

    public function testGetOid()
    {
        $algo = 'sha256';
        $oid = HashAlgorithmOidMapper::getHashAlgorithmOid($algo);
        $this->assertEquals('2.16.840.1.101.3.4.2.1', $oid->getContent());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetOidButUnkown()
    {
        $algo = 'sha25000';
        $oid = HashAlgorithmOidMapper::getHashAlgorithmOid($algo);
        $this->assertEquals('2.16.840.1.101.3.4.2.1', $oid->getContent());
    }

    public function testGetHasher()
    {
        $algo = 'sha256';
        $math = new Gmp();
        $oid = HashAlgorithmOidMapper::getHashAlgorithmOid($algo);
        $hasher = HashAlgorithmOidMapper::getHasherFromOid($math, $oid);
        $this->assertEquals($algo, $hasher->getAlgo());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetHasherButUnkown()
    {
        $math = new Gmp();
        $oid = new ObjectIdentifier('1.1.1');
        HashAlgorithmOidMapper::getHasherFromOid($math, $oid);
    }

    public function testGetByteSize()
    {
        $this->assertEquals(64, HashAlgorithmOidMapper::getByteSize('sha256'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetByteSizeButUnkown()
    {
        HashAlgorithmOidMapper::getByteSize('sha256aaaaa');
    }
}