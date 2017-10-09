<?php

namespace Mdanter\Ecc\Tests\Crypto;

use Mdanter\Ecc\Message\MessageFactory;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
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

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid ECDH exchange - Point does not exist on our curve
     */
    public function testChecksCurveMismatch()
    {
        $g521Priv = gmp_init("933647627474908018426578245710479111318013963124904148836279534969474325811737975019251749444245449462797733969359656644867805138716790671286350292237562679", 10);
        $p1 = EccFactory::getNistCurves()->generator521()->getPrivateKeyFrom($g521Priv);

        $g192Pub = "0468e3642493c4e433a741c78ab67ee607d94925c506e9554d43de2d1c71493334c681cf4683aee863d90e9732745d5bc7";
        $g192 = EccFactory::getNistCurves()->generator192();

        $adapter = EccFactory::getAdapter();
        $p2 = (new UncompressedPointSerializer($adapter))->unserialize($g192->getCurve(), $g192Pub);
        $pubkey = $g192->getPublicKeyFrom($p2->getX(), $p2->getY());

        $p1
            ->createExchange(new MessageFactory($adapter), $pubkey)
            ->calculateSharedKey()
        ;
    }
}
