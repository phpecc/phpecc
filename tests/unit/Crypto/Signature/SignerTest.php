<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Crypto\Signature;

use Mdanter\Ecc\Crypto\Signature\SignHasher;
use Mdanter\Ecc\Tests\AbstractTestCase;

class SignerTest extends AbstractTestCase
{
    public function testInvalidHashAlgorithm()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported hashing algorithm');
        new SignHasher("blahblah");
    }
}
