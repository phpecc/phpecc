<?php

namespace Mdanter\Ecc\Tests\Serializer\Signature;

use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\Sequence;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class SignatureTest extends AbstractTestCase
{
    public function testParse()
    {
        $hex = hex2bin('3044022003bc1c8fff45c20f37c114d4a9290dbc7b651778bc2f5b703175f39951b9122c022040932d7f8f16ea7fb1221afb3c97d1be516a54be723923b185cc8d95b6a2e36e');
        $ser = new DerSignatureSerializer();
        $parsed = $ser->parse($hex);
        $bin = $ser->serialize($parsed);
        $this->assertEquals($hex, $bin);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRejectsNotSequence()
    {
        $int = new Integer(1);
        $ser = new DerSignatureSerializer();
        $ser->parse($int->getBinary());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRejectsOnlyOneInteger()
    {
        $int = new Sequence(
            new Integer(1)
        );

        $ser = new DerSignatureSerializer();
        $ser->parse($int->getBinary());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRejectsNotInteger()
    {
        $int = new Sequence(
            new BitString('00'),
            new BitString('00')
        );

        $ser = new DerSignatureSerializer();
        $ser->parse($int->getBinary());
    }
}