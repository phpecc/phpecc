<?php

namespace Mdanter\Ecc\Tests\Random;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Key\PrivateKey;
use Mdanter\Ecc\Random\HmacRandomNumberGenerator;
use Mdanter\Ecc\Tests\AbstractTestCase;

class HmacRandomNumberGeneratorTest extends AbstractTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unsupported hashing algorithm
     */
    public function testRequireValidAlgorithm()
    {
        $math = EccFactory::getAdapter();
        $g = EccFactory::getNistCurves()->generator192();
        $privateKey  = new PrivateKey($math, $g, 1);
        $hash = hash('sha256', 'message', true);

        new HmacRandomNumberGenerator($math, $privateKey, $hash, 'sha256aaaa');
    }

}
