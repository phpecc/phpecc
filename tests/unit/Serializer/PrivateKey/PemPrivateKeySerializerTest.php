<?php

namespace Mdanter\Ecc\Tests\Serializer\PrivateKey;

use Mdanter\Ecc\Crypto\Key\PrivateKey;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class PemPrivateKeySerializerTest extends AbstractTestCase
{
    public function testReadsDer()
    {
        $der = file_get_contents(__DIR__ . "/../../../data/openssl-priv.pem");
        $adapter = EccFactory::getAdapter();
        $derSerializer = new DerPrivateKeySerializer($adapter);
        $pemSerializer = new PemPrivateKeySerializer($derSerializer);
        $key = $pemSerializer->parse($der);
        $this->assertInstanceOf(PrivateKey::class, $key);
    }


    public function testConsistent()
    {
        $adapter = EccFactory::getAdapter();
        $G = EccFactory::getNistCurves($adapter)->generator192();
        $key = $G->createPrivateKey();

        $derPrivSerializer = new DerPrivateKeySerializer($adapter);
        $pemSerializer = new PemPrivateKeySerializer($derPrivSerializer);

        $serialized = $pemSerializer ->serialize($key);
        $parsed = $pemSerializer ->parse($serialized);
        $this->assertTrue($adapter->equals($parsed->getSecret(), $key->getSecret()));
    }
}
