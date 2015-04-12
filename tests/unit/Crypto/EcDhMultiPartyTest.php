<?php

namespace Mdanter\Ecc\Tests\Crypto;

use Mdanter\Ecc\Message\MessageFactory;
use Mdanter\Ecc\Tests\AbstractTestCase;
use Mdanter\Ecc\EccFactory;

class EcDhMultiPartyTest extends AbstractTestCase
{

    public function testMultiPartyKeyGeneration()
    {
        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getNistCurves($adapter)->generator256();
        $messages = new MessageFactory($adapter);
        $alice = $generator->createPrivateKey();
        $bob = $generator->createPrivateKey();
        $carol = $generator->createPrivateKey();

        // Alice computes g^a and sends it to Bob.
        $bobX = $alice->createExchange($messages, $bob->getPublicKey());
        // Bob computes (g^a)^b = g^{ab} and sends it to Carol.
        $carolX = $carol->createExchange($messages, $bobX->createMultiPartyKey());
        // Carol computes (g^{ab})^c = g^{abc} and uses it as her secret.
        $carolSharedKey = $carolX->calculateSharedKey();

        // Bob computes g^b and sends it to Carol.
        $carolX = $carol->createExchange($messages, $bob->getPublicKey());
        // Carol computes (g^b)^c = g^{bc} and sends it to Alice.
        $aliceX = $alice->createExchange($messages, $carolX->createMultiPartyKey());
        // Alice computes (g^{bc})^a = g^{bca} = g^{abc} and uses it as her secret.
        $aliceSharedKey = $aliceX->calculateSharedKey();

        // Carol computes g^c and sends it to Alice.
        $aliceX = $carol->createExchange($messages, $alice->getPublicKey());
        // Alice computes (g^c)^a = g^{ca} and sends it to Bob.
        $bobX = $bob->createExchange($messages, $aliceX->createMultiPartyKey());
        // Bob computes (g^{ca})^b = g^{cab} = g^{abc} and uses it as his secret.
        $bobSharedKey = $bobX->calculateSharedKey();

        $this->assertTrue($bobSharedKey == $aliceSharedKey);
        $this->assertTrue($aliceSharedKey == $carolSharedKey);
        $this->assertTrue($carolSharedKey == $bobSharedKey);
    }
}
