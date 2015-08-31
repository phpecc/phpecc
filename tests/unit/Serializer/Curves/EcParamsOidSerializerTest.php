<?php

namespace Mdanter\Ecc\Tests\Serializer\Curves;


use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\Curves\EcParamsOidSerializer;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class EcParamsOidSerializerTest extends AbstractTestCase
{
    public function getVectors()
    {
        return [
            [
                'secp112r1',
                '-----BEGIN EC PARAMETERS-----
BgUrgQQABg==
-----END EC PARAMETERS-----'
            ],
            [
                'secp256k1',
                '-----BEGIN EC PARAMETERS-----
BgUrgQQACg==
-----END EC PARAMETERS-----'
            ],
            [
                'secp256r1',
                '-----BEGIN EC PARAMETERS-----
BggqhkjOPQMBBw==
-----END EC PARAMETERS-----'
            ],
            [
                'secp384r1',
                '-----BEGIN EC PARAMETERS-----
BgUrgQQAIg==
-----END EC PARAMETERS-----'
            ],
            [
                'nist-p192',
                '-----BEGIN EC PARAMETERS-----
BggqhkjOPQMBAQ==
-----END EC PARAMETERS-----'
            ],
            [
                'nist-p224',
                '-----BEGIN EC PARAMETERS-----
BgUrgQQAIQ==
-----END EC PARAMETERS-----'
            ],
            [
                'nist-p256',
                '-----BEGIN EC PARAMETERS-----
BggqhkjOPQMBBw==
-----END EC PARAMETERS-----'
            ],
            [
                'nist-p384',
                '-----BEGIN EC PARAMETERS-----
BgUrgQQAIg==
-----END EC PARAMETERS-----'
            ],
            [
                'nist-p521',
                '-----BEGIN EC PARAMETERS-----
BgUrgQQAIw==
-----END EC PARAMETERS-----'
            ],
        ];
    }

    /**
     * @dataProvider getVectors
     */
    public function testAgainstOpenSSL($curveName, $expectedParams)
    {
        $math = EccFactory::getAdapter();
        $curve = CurveFactory::getCurveByName($curveName);
        $G = CurveFactory::getGeneratorByName($curveName);

        $ser = new EcParamsOidSerializer();
        $this->assertEquals($expectedParams, $ser->serialize($curve));
    }
}