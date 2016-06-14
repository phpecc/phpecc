<?php

namespace Mdanter\Ecc\Tests\Crypto\EcDH;

use Mdanter\Ecc\Crypto\EcDH\EcDH;
use Mdanter\Ecc\EccFactory;
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
}
