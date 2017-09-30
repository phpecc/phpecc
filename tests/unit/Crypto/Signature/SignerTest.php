<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Crypto\Signature;

use Mdanter\Ecc\Crypto\Signature\SignHasher;
use Mdanter\Ecc\Tests\AbstractTestCase;

class SignerTest extends AbstractTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unsupported hashing algorithm
     */
    public function testInvalidHashAlgorithm()
    {
        new SignHasher("blahblah");
    }
}
