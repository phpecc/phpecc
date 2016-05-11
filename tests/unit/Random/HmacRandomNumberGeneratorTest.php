<?php

namespace Mdanter\Ecc\Tests\Random;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Key\PrivateKey;
use Mdanter\Ecc\Random\Hmac;
use Mdanter\Ecc\Tests\AbstractTestCase;

class HmacRandomNumberGeneratorTest extends AbstractTestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Hashing algorithm not found
     */
    public function testRequireValidAlgorithm()
    {
        $math = EccFactory::getAdapter();
        $g = EccFactory::getNistCurves()->generator192();
        $privateKey  = new PrivateKey($math, $g, 1);
        $hash = hash('sha256', 'message', true);

        new Hmac($math, $privateKey, $hash, 'sha256aaaa');
    }

}
