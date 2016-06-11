<?php

namespace Mdanter\Ecc\Util;

use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Math\GmpMathInterface;

class NumberSize
{

    /**
     * @param GmpMathInterface $adapter
     * @param resource|\GMP    $x
     * @return float
     */
    public static function getCeiledByteSize(GmpMathInterface $adapter, $x)
    {
        if (!GmpMath::checkGmpValue($x)) {
            throw new \InvalidArgumentException('Invalid argument #2 for NumberSize::getCeiledByteSize - must pass GMP resource or \GMP instance');
        }

        $log2 = 0;
        while ($x = $adapter->rightShift($x, 1)) {
            $log2++;
        }

        return ceil($log2 / 8);
    }

    /**
     * @param GmpMathInterface $adapter
     * @param resource|\GMP    $x
     * @return float
     */
    public static function getFlooredByteSize(GmpMathInterface $adapter, $x)
    {
        if (!GmpMath::checkGmpValue($x)) {
            throw new \InvalidArgumentException('Invalid argument #2 for NumberSize::getFlooredByteSize - must pass GMP resource or \GMP instance');
        }

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
     * @param  resource|\GMP    $x
     * @return number
     *
     * @link https://www.openssl.org/docs/crypto/BN_num_bytes.html
     */
    public static function bnNumBytes(GmpMathInterface $adapter, $x)
    {

        if (!GmpMath::checkGmpValue($x)) {
            throw new \InvalidArgumentException('Invalid argument #2 for NumberSize::bnNumBytes - must pass GMP resource or \GMP instance');
        }

        // https://github.com/luvit/openssl/blob/master/openssl/crypto/bn/bn.h#L402
        return floor((self::bnNumBits($adapter, $x) + 7) / 8);
    }

    /**
     * Returns the number of bits used to store this number. Non-singicant upper bits are not counted.
     *
     * @param  GmpMathInterface $adapter
     * @param  resource|\GMP    $x
     * @return number
     *
     * @link https://www.openssl.org/docs/crypto/BN_num_bytes.html
     */
    public static function bnNumBits(GmpMathInterface $adapter, $x)
    {
        if (!GmpMath::checkGmpValue($x)) {
            throw new \InvalidArgumentException('Invalid argument #2 for NumberSize::bnNumBits - must pass GMP resource or \GMP instance');
        }

        $zero = gmp_init(0, 10);
        if ($adapter->cmp($x, $zero) == 0) {
            return 0;
        }

        $log2 = 0;
        while ($adapter->cmp($x, $zero) != 0) {
            $x = $adapter->rightShift($x, 1);
            $log2++;
        }

        return $log2 ;
    }
}
