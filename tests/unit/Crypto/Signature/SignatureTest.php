<?php

namespace Mdanter\Ecc\Tests\Crypto\Signature;

use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Tests\AbstractTestCase;

class SignatureTest extends AbstractTestCase
{
    public function testInstance()
    {
        $r = gmp_init(10);
        $s = gmp_init(20);
        $signature = new Signature($r, $s);
        $this->assertSame($r, $signature->getR());
        $this->assertSame($s, $signature->getS());
    }
}
