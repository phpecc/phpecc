<?php

namespace Mdanter\Ecc\Tests\Serializer\Signature;

use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Sequence;
use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class DerSignatureSerializerTest extends AbstractTestCase
{
    public function testParsesSignature()
    {
        $r = gmp_init('15012732708734045374201164973195778115424038544478436215140305923518805725225', 10);
        $s = gmp_init('32925333523544781093325025052915296870609904100588287156912210086353851961511', 10);
        $expected = strtolower('304402202130E7D504C4A498C3B3C7C0FED6DE2A84811A3BD89BADB8627658F2B1EA5029022048CB1410308E3EFC512B4CE0974F6D0869E9454095C8855ABEA6B6325A40D0A7');
        $signature = new Signature($r, $s);
        $serializer = new DerSignatureSerializer();
        $serialized = bin2hex($serializer->serialize($signature));
        $this->assertEquals($expected, $serialized);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid data
     */
    public function testInvalidASN1()
    {
        // Bitstring is not a sequence..
        $bitString = new BitString('ab');
        $binary = $bitString->getBinary();
        $serializer = new DerSignatureSerializer();
        $serializer->parse($binary);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Failed to parse signature
     */
    public function testInvalidASN2()
    {
        // Sequence != 2 items
        $sequence = new Sequence();
        $binary = $sequence->getBinary();
        $serializer = new DerSignatureSerializer();
        $serializer->parse($binary);
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Failed to parse signature
     */
    public function testInvalidASN3()
    {
        // bitstring isn't an integer
        $sequence = new Sequence(
            new BitString('41'),
            new BitString('ab')
        );
        $binary = $sequence->getBinary();
        $serializer = new DerSignatureSerializer();
        $serializer->parse($binary);
    }

    public function testIsConsistent()
    {
        $math = new GmpMath();
        $rbg = RandomGeneratorFactory::getRandomGenerator();
        $serializer = new DerSignatureSerializer();

        $i = 256;
        $max = $math->sub($math->pow(gmp_init(2, 10), $i), gmp_init(1, 10));
        $r = $rbg->generate($max);
        $s = $rbg->generate($max);
        $signature = new Signature($r, $s);

        $serialized = $serializer->serialize($signature);
        $parsed = $serializer->parse($serialized);

        $this->assertTrue($math->equals($signature->getR(), $parsed->getR()));
        $this->assertTrue($math->equals($signature->getS(), $parsed->getS()));
    }
}
