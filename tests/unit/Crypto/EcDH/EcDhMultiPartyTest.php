<?php

namespace Mdanter\Ecc\Tests\Crypto\EcDH;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Tests\AbstractTestCase;

class EcDhMultiPartyTest extends AbstractTestCase
{

    public function testMultiPartyKeyGeneration()
    {
        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getNistCurves($adapter)->generator256();
        $alice = $generator->createPrivateKey();
        $bob = $generator->createPrivateKey();
        $carol = $generator->createPrivateKey();

        // Alice computes g^a and sends it to Bob.
        $bobX = $alice->createExchange($bob->getPublicKey());
        // Bob computes (g^a)^b = g^{ab} and sends it to Carol.
        $carolX = $carol->createExchange($bobX->createMultiPartyKey());
        // Carol computes (g^{ab})^c = g^{abc} and uses it as her secret.
        $carolSharedKey = $carolX->calculateSharedKey();

        // Bob computes g^b and sends it to Carol.
        $carolX = $carol->createExchange($bob->getPublicKey());
        // Carol computes (g^b)^c = g^{bc} and sends it to Alice.
        $aliceX = $alice->createExchange($carolX->createMultiPartyKey());
        // Alice computes (g^{bc})^a = g^{bca} = g^{abc} and uses it as her secret.
        $aliceSharedKey = $aliceX->calculateSharedKey();

        // Carol computes g^c and sends it to Alice.
        $aliceX = $carol->createExchange($alice->getPublicKey());
        // Alice computes (g^c)^a = g^{ca} and sends it to Bob.
        $bobX = $bob->createExchange($aliceX->createMultiPartyKey());
        // Bob computes (g^{ca})^b = g^{cab} = g^{abc} and uses it as his secret.
        $bobSharedKey = $bobX->calculateSharedKey();

        $this->assertTrue($adapter->equals($bobSharedKey, $aliceSharedKey));
        $this->assertTrue($adapter->equals($aliceSharedKey, $carolSharedKey));
        $this->assertTrue($adapter->equals($carolSharedKey, $bobSharedKey));
    }
}
