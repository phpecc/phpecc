<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Serializer\Signature;

use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\Signature\HexSignatureSerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class HexSignatureSerializerTest extends AbstractTestCase
{
    public function testParsesSignature()
    {
        $r = gmp_init('15012732708734045374201164973195778115424038544478436215140305923518805725225', 10);
        $s = gmp_init('32925333523544781093325025052915296870609904100588287156912210086353851961511', 10);
        $expected = strtolower('2130e7d504c4a498c3b3c7c0fed6de2a84811a3bd89badb8627658f2b1ea502948cb1410308e3efc512b4ce0974f6d0869e9454095c8855abea6b6325a40d0a7');
        $signature = new Signature($r, $s);
        $serializer = new HexSignatureSerializer();
        $serialized = $serializer->serialize($signature);
        $this->assertEquals($expected, $serialized);
    }

    public function testInvalidHex()
    {
        $this->expectException(\Mdanter\Ecc\Exception\SignatureDecodeException::class);
        $this->expectExceptionMessage('Invalid hex string.');
        // Not a valid hex string
        $hexstring = 'xyz';
        $serializer = new HexSignatureSerializer();
        $serializer->parse($hexstring);
    }

    public function testInvalidHexSig()
    {
        $this->expectException(\Mdanter\Ecc\Exception\SignatureDecodeException::class);
        $this->expectExceptionMessage('Invalid data.');
        // Hexstring too short
        $hexstring = 'abc';
        $serializer = new HexSignatureSerializer();
        $serializer->parse($hexstring);
    }

    public function testInvalidHexSig2()
    {
        $this->expectException(\Mdanter\Ecc\Exception\SignatureDecodeException::class);
        $this->expectExceptionMessage('Invalid data.');
        // Hexstring with too many parts
        $hexstring = 'abc|123|def';
        $serializer = new HexSignatureSerializer();
        $serializer->parse($hexstring);
    }

    public function testIsConsistent()
    {
        $math = new GmpMath();
        $rbg = RandomGeneratorFactory::getRandomGenerator();
        $serializer = new HexSignatureSerializer();

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
