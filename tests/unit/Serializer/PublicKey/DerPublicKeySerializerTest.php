<?php

namespace Mdanter\Ecc\Tests\Serializer\PublicKey;

use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Primitives\CurveFp;
use Mdanter\Ecc\Primitives\CurveParameters;
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

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not implemented for unnamed curves
     */
    public function testInvalidCurve()
    {
        $adapter = EccFactory::getAdapter();
        $p = gmp_init('6277101735386680763835789423207666416083908700390324961279', 10);
        $b = gmp_init('64210519e59c80e70fa7e9ab72243049feb8deecc146b9b1', 16);

        $parameters = new CurveParameters(192, $p, gmp_init('-3', 10), $b);
        $curve = new CurveFp($parameters, $adapter);

        $order = gmp_init('6277101735386680763835789423176059013767194773182842284081', 10);

        $x = gmp_init('188da80eb03090f67cbf20eb43a18800f4ff0afd82ff1012', 16);
        $y = gmp_init('07192b95ffc8da78631011ed6b24cdd573f977a11e794811', 16);

        $generator = $curve->getGenerator($x, $y, $order);
        $private = $generator->getPrivateKeyFrom(gmp_init(12));
        $public = $private->getPublicKey();

        $serializer = new DerPublicKeySerializer();
        $serializer->serialize($public);

    }
}
