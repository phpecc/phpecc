<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Crypto\EcDH;

use Mdanter\Ecc\Crypto\EcDH\EcDH;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Primitives\Point;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class EcDHTest extends AbstractTestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Sender key not set
     */
    public function testExceptionOnInvalidState()
    {
        $adapter = EccFactory::getAdapter();
        $ecdh = new EcDH($adapter);
        $ecdh->calculateSharedKey();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Recipient key not set
     */
    public function testExceptionOnInvalidState1()
    {
        $G = EccFactory::getNistCurves()->generator521();
        $adapter = EccFactory::getAdapter();
        $ecdh = new EcDH($adapter);
        $ecdh->setSenderKey($G->createPrivateKey());
        $ecdh->calculateSharedKey();
    }

    public function testNoExceptionWhenCorrectState()
    {
        $G = EccFactory::getNistCurves()->generator521();
        $adapter = EccFactory::getAdapter();
        $ecdh = new EcDH($adapter);
        $ecdh->setSenderKey($G->createPrivateKey());
        $ecdh->setRecipientKey($G->createPrivateKey()->getPublicKey());

        // Call twice, covers checking if shared key already created
        $this->assertInstanceOf(\GMP::class, $ecdh->calculateSharedKey());
        $this->assertInstanceOf(\GMP::class, $ecdh->calculateSharedKey());
    }

    public function testChecksCurveMismatch()
    {
        $g521Priv = gmp_init("933647627474908018426578245710479111318013963124904148836279534969474325811737975019251749444245449462797733969359656644867805138716790671286350292237562679", 10);
        $p1 = EccFactory::getNistCurves()->generator521()->getPrivateKeyFrom($g521Priv);

        $g192Pub = "0468e3642493c4e433a741c78ab67ee607d94925c506e9554d43de2d1c71493334c681cf4683aee863d90e9732745d5bc7";
        $g192 = EccFactory::getNistCurves()->generator192();

        $p2 = (new UncompressedPointSerializer())->unserialize($g192->getCurve(), $g192Pub);
        $pubkey = $g192->getPublicKeyFrom($p2->getX(), $p2->getY());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Invalid ECDH exchange - Point does not exist on our curve");

        $p1
            ->createExchange($pubkey)
            ->calculateSharedKey()
        ;
    }
}
