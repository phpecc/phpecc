<?php

namespace Mdanter\Ecc\Tests\Serializer;

use FG\ASN1\Universal\Integer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class DerPublicKeySerializerTest extends AbstractTestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid data.
     */
    public function testFirstFailure()
    {
        $asn = new Integer(1);
        $binary = $asn->getBinary();

        $serializer = new DerPublicKeySerializer();
        $serializer->parse($binary);
    }
}
