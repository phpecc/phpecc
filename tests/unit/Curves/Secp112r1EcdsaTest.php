<?php

namespace Mdanter\Ecc\Tests\Curves;


use Mdanter\Ecc\Tests\AbstractTestCase;

class Secp112r1EcdsaTest extends AbstractTestCase
{
    # https://github.com/johndoe31415/joeecc/blob/28e112174b924dd264f43b82577a4e5ca07e66df/ecc/tests/CryptoOpsTests.py#L34
    public function testEcdsaOnSecp112r1()
    {
        $expectedR = '1696427335541514286367855701829018';
        $expectedS = '1960761230049936699759766101723490';

        $adapter = \Mdanter\Ecc\EccFactory::getAdapter();
        $g = \Mdanter\Ecc\EccFactory::getSecgCurves()->generator112r1();

        $key = gmp_init('deadbeef', 16);
        $priv = $g->getPrivateKeyFrom($key);

        $data = "foobar";
        $signer = new \Mdanter\Ecc\Crypto\Signature\Signer($adapter);
        $hash = $signer->hashData($g, "sha1", $data);
        $randomK = gmp_init('12345', 10);

        $signature = $signer->sign($priv, $hash, $randomK);
        $this->assertEquals($expectedR, $adapter->toString($signature->getR()));
        $this->assertEquals($expectedS, $adapter->toString($signature->getS()));

        $this->assertTrue($signer->verify($priv->getPublicKey(), $signature, $hash));
    }
}