<?php

namespace Mdanter\Ecc\Tests\Serializer\PublicKey;

use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
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

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid data: non X509 data.
     */
    public function testInvalidEcdsaOid()
    {

        $sequence = new Sequence(
            new Sequence(
                new ObjectIdentifier('1.1.1.1.1'),
                CurveOidMapper::getCurveOid(CurveFactory::getCurveByName('nistp192'))
            ),
            new BitString('04188DA80EB03090F67CBF20EB43A18800F4FF0AFD82FF101207192B95FFC8DA78631011ED6B24CDD573F977A11E794811')
        );
        $binary = $sequence->getBinary();

        $serializer = new DerPublicKeySerializer();
        $serializer->parse($binary);

    }
}
