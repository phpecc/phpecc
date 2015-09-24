<?php
/**
 * Created by PhpStorm.
 * User: aeonium
 * Date: 24/09/15
 * Time: 13:49
 */

namespace Mdanter\Ecc\Tests\Serializer;


use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class DerSignatureSerializerTest extends AbstractTestCase
{
    public function testParsesSignature()
    {
        $r = '15012732708734045374201164973195778115424038544478436215140305923518805725225';
        $s = '32925333523544781093325025052915296870609904100588287156912210086353851961511';
        $expected = strtolower('304402202130E7D504C4A498C3B3C7C0FED6DE2A84811A3BD89BADB8627658F2B1EA5029022048CB1410308E3EFC512B4CE0974F6D0869E9454095C8855ABEA6B6325A40D0A7');
        $signature = new Signature($r, $s);
        $serializer = new DerSignatureSerializer();
        $serialized = bin2hex($serializer->serialize($signature));
        $this->assertEquals($expected, $serialized);
    }

    public function testIsConsistent()
    {
        $math = new Gmp();
        $rbg = RandomGeneratorFactory::getUrandomGenerator();
        $serializer = new DerSignatureSerializer();

        for ($i = 2; $i <= 521; $i++) {
            $max = $math->sub($math->pow(2, $i), 1);
            $r = $rbg->generate($max);
            $s = $rbg->generate($max);
            $signature = new Signature($r, $s);

            $serialized = $serializer->serialize($signature);
            $parsed = $serializer->parse($serialized);

            $this->assertEquals($signature, $parsed);
        }
    }
}