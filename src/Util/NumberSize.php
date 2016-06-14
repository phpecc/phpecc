<?php

namespace Mdanter\Ecc\Util;

use Mdanter\Ecc\Math\GmpMathInterface;

class NumberSize
{

    /**
     * @param GmpMathInterface $adapter
     * @param \GMP             $x
     * @return float
     */
    public static function getCeiledByteSize(GmpMathInterface $adapter, \GMP $x)
    {
        $log2 = 0;
        while ($x = $adapter->rightShift($x, 1)) {
            $log2++;
        }

        return ceil($log2 / 8);
    }

    /**
     * @param GmpMathInterface $adapter
     * @param \GMP             $x
     * @return float
     */
    public static function getFlooredByteSize(GmpMathInterface $adapter, \GMP $x)
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
     * @param  GmpMathInterface $adapter
     * @param  \GMP             $x
     * @return integer
     *
     * @link https://www.openssl.org/docs/crypto/BN_num_bytes.html
     */
    public static function bnNumBytes(GmpMathInterface $adapter, \GMP $x)
    {
        // https://github.com/luvit/openssl/blob/master/openssl/crypto/bn/bn.h#L402
        return floor((self::bnNumBits($adapter, $x) + 7) / 8);
    }

    /**
     * Returns the number of bits used to store this number. Non-singicant upper bits are not counted.
     *
     * @param  GmpMathInterface $adapter
     * @param  \GMP             $x
     * @return number
     *
     * @link https://www.openssl.org/docs/crypto/BN_num_bytes.html
     */
    public static function bnNumBits(GmpMathInterface $adapter, \GMP $x)
    {
        $zero = gmp_init(0, 10);
        if ($adapter->equals($x, $zero)) {
            return 0;
        }

        $log2 = 0;
        while (false === $adapter->equals($x, $zero)) {
            $x = $adapter->rightShift($x, 1);
            $log2++;
        }

        return $log2 ;
    }
}
