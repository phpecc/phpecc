<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Serializer\Signature;

use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\Signature\IEEEP1363Serializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class IEEEP1363SerializerTest extends AbstractTestCase
{
    public function testParsesSignature()
    {
        $r = gmp_init('15012732708734045374201164973195778115424038544478436215140305923518805725225', 10);
        $s = gmp_init('32925333523544781093325025052915296870609904100588287156912210086353851961511', 10);
        $expected = '2130e7d504c4a498c3b3c7c0fed6de2a84811a3bd89badb8627658f2b1ea5029' .
            '48cb1410308e3efc512b4ce0974f6d0869e9454095c8855abea6b6325a40d0a7';
        $signature = new Signature($r, $s);
        $serializer = new IEEEP1363Serializer((new NistCurve(new GmpMath()))->curve256());
        $serialized = bin2hex($serializer->serialize($signature));
        $this->assertEquals($expected, $serialized);
    }

    public function testIsConsistent()
    {
        $math = new GmpMath();
        $rbg = RandomGeneratorFactory::getRandomGenerator();
        $serializer = new IEEEP1363Serializer();

        $i = 256;
        $max = $math->sub($math->pow(gmp_init(2, 10), $i), gmp_init(1, 10));
        $r = $rbg->generate($max);
        $s = $rbg->generate($max);
        $signature = new Signature($r, $s);

        $serialized = $serializer->serialize($signature);
        $parsed = $serializer->parse($serialized);

        $this->assertTrue($math->equals($signature->getR(), $parsed->getR()));
        $this->assertTrue($math->equals($signature->getS(), $parsed->getS()));

        // Let's test with an explicit size:
        $serialized = $serializer->serialize($signature, 384);
        $parsed = $serializer->parse($serialized);

        $this->assertTrue($math->equals($signature->getR(), $parsed->getR()));
        $this->assertTrue($math->equals($signature->getS(), $parsed->getS()));
    }
}
