<?php

namespace Mdanter\Ecc\Tests\Serializer\Point;


use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\Point\CompressedPointSerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class CompressedPointSerializerTest extends AbstractTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid data: only compressed keys are supported.
     */
    public function testChecksPrefix()
    {
        $data = '01aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $serializer = new CompressedPointSerializer(EccFactory::getAdapter());
        $serializer->unserialize(EccFactory::getNistCurves()->curve192(), $data);
    }
}