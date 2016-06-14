<?php

namespace Mdanter\Ecc\Tests\Serializer;

use Mdanter\Ecc\Crypto\Key\PrivateKey;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class DerPrivateKeySerializerTest extends AbstractTestCase
{
    public function testReadsDer()
    {
        $der = base64_decode(file_get_contents(__DIR__ . "/../../data/openssl-priv.key"));
        $adapter = EccFactory::getAdapter();
        $derPrivSerializer = new DerPrivateKeySerializer($adapter);
        $key = $derPrivSerializer->parse($der);
        $this->assertInstanceOf(PrivateKey::class, $key);
    }

    public function testConsistent()
    {
        $adapter = EccFactory::getAdapter();
        $G = EccFactory::getNistCurves($adapter)->generator192();
        $key = $G->createPrivateKey();

        $derPrivSerializer = new DerPrivateKeySerializer($adapter);
        $serialized = $derPrivSerializer->serialize($key);
        $parsed = $derPrivSerializer->parse($serialized);
        $this->assertTrue($adapter->equals($parsed->getSecret(), $key->getSecret()));
    }
}
