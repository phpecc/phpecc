<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Serializer\Point;

use Mdanter\Ecc\EccFactory;
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
}
