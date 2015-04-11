<?php

namespace Mdanter\Ecc\Util;

use Mdanter\Ecc\Math\MathAdapterInterface;

class NumberSize
{

    public static function getCeiledByteSize(MathAdapterInterface $adapter, $x)
    {
        $log2 = 0;

        while ($x = $adapter->rightShift($x, 1)) {
            $log2++;
        }

        return ceil($log2 / 8);
    }

    public static function getFlooredByteSize(MathAdapterInterface $adapter, $x)
    {
        $log2 = 0;

        while ($x = $adapter->rightShift($x, 1)) {
            $log2++;
        }

        return floor($log2 / 8) + 1;
    }

    /**
     * Returns the number of mininum required bytes to store a given number. Non-significant upper bits are not counted.
     *
     * @param  MathAdapterInterface $adapter
     * @param  int|string           $x
     * @return number
     *
     * @link https://www.openssl.org/docs/crypto/BN_num_bytes.html
     */
    public static function bnNumBytes(MathAdapterInterface $adapter, $x)
    {
        // https://github.com/luvit/openssl/blob/master/openssl/crypto/bn/bn.h#L402
        return floor((self::bnNumBits($adapter, $x) + 7) / 8);
    }

    /**
     * Returns the number of bits used to store this number. Non-singicant upper bits are not counted.
     *
     * @param  MathAdapterInterface $adapter
     * @param  int|string           $x
     * @return number
     *
     * @link https://www.openssl.org/docs/crypto/BN_num_bytes.html
     */
    public static function bnNumBits(MathAdapterInterface $adapter, $x)
    {
        if ($adapter->cmp($x, '0') == 0) {
            return 0;
        }

        $log2 = 0;

        while ($x = $adapter->rightShift($x, 1)) {
            $log2++;
        }

        return $log2 + 1;
    }
}
