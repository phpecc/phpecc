<?php

namespace Mdanter\Ecc\Tests\Serializer;


use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class PemPublicKeySerializerTest extends AbstractTestCase
{

    public function testConsistent()
    {
        $adapter = EccFactory::getAdapter();
        $G = EccFactory::getNistCurves($adapter)->generator192();
        $pubkey = $G->createPrivateKey()->getPublicKey();

        $serializer = new PemPublicKeySerializer(new DerPublicKeySerializer($adapter));
        $serialized = $serializer->serialize($pubkey);
        $parsed = $serializer->parse($serialized);
        $this->assertTrue($pubkey->getPoint()->equals($parsed->getPoint()));
        $this->assertEquals($pubkey->getCurve(), $parsed->getCurve());
        $this->assertEquals($pubkey->getGenerator(), $parsed->getGenerator());
    }
}