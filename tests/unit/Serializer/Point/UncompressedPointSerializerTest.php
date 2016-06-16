<?php

namespace Mdanter\Ecc\Tests\Serializer\Point;


use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class UncompressedPointSerializerTest extends AbstractTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid data: only uncompressed keys are supported.
     */
    public function testChecksPrefix()
    {
        $data = '01aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $serializer = new UncompressedPointSerializer(EccFactory::getAdapter());
        $serializer->unserialize(EccFactory::getNistCurves()->curve192(), $data);
    }
}