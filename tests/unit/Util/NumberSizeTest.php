<?php

namespace Mdanter\Ecc\Tests\Util;

use Mdanter\Ecc\Tests\AbstractTestCase;
use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Util\NumberSize;

class NumberSizeTest extends AbstractTestCase
{

    public function getBnNumBitsNumbers()
    {
        return $this->_getAdapters(array(
            array('0', 0),
            array('0x100', 9),
            array('0x00000432', 11),
        ));
    }

    /**
     * @dataProvider getBnNumBitsNumbers
     */
    public function testNumBits(MathAdapterInterface $adapter, $number, $expected)
    {
        $size = NumberSize::bnNumBits($adapter, $adapter->hexDec($number));

        $this->assertEquals($expected, $size);
    }

    public function getBnNumBytesNumbers()
    {
        return $this->_getAdapters(array(
            array('0', 0),
            array('0x00000432', 2),
            array('0x2e224bd065fead1218f3608d4e74837b6096d11c4fff4139cd41d9df03cfcb270df7a9ae6f628819c3ae744db4189b1330cb2ee4eea7d5515b282dee59e21dcf1e', 65),
        ));
    }

    /**
     * @dataProvider getBnNumBytesNumbers
     */
    public function testNumBytes(MathAdapterInterface $adapter, $number, $expected)
    {
        $size = NumberSize::bnNumBytes($adapter, $adapter->hexDec($number));

        $this->assertEquals($expected, $size);
    }
}
