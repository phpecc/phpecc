<?php

namespace Mdanter\Ecc\Tests\Crypto\Signature;


use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Tests\AbstractTestCase;

class SignerTest extends AbstractTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unsupported hashing algorithm
     */
    public function testInvalidHashAlgorithm()
    {
        $adapter = new GmpMath();
        $generator = EccFactory::getNistCurves()->generator192();
        $signer = new Signer($adapter);
        $signer->hashData($generator, 'blahblah', 'message to be signed');
    }
}