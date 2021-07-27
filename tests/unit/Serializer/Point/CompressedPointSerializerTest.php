<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Serializer\Point;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Serializer\Point\CompressedPointSerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class CompressedPointSerializerTest extends AbstractTestCase
{
    public function testChecksPrefix()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid data: only compressed keys are supported.');
        $data = '01aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $serializer = new CompressedPointSerializer(EccFactory::getAdapter());
        $serializer->unserialize(EccFactory::getNistCurves()->curve192(), $data);
    }

    public function testSerializesToActualPoint()
    {
        $adapter = new GmpMath();
        $generator = EccFactory::getNistCurves()->generator384();
        $sk = $generator->createPrivateKey();
        $pk = $sk->getPublicKey();

        // Compress a point
        $serializer = new CompressedPointSerializer($adapter);
        $compressed = $serializer->serialize($pk->getPoint());

        // Uncompress the point
        $recovered = $serializer->unserialize(
            EccFactory::getNistCurves()->curve384(),
            $compressed
        );

        $this->assertSame(
            gmp_strval($pk->getPoint()->getX(), 16),
            gmp_strval($recovered->getX(), 16)
        );

        $this->assertSame(
            gmp_strval($pk->getPoint()->getY(), 16),
            gmp_strval($recovered->getY(), 16)
        );
    }
}
