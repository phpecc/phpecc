<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Serializer\Point;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class UncompressedPointSerializerTest extends AbstractTestCase
{
    public function testChecksPrefix()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid data: only uncompressed keys are supported.');
        $data = '01aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $serializer = new UncompressedPointSerializer();
        $serializer->unserialize(EccFactory::getNistCurves()->curve192(), $data);
    }
}
