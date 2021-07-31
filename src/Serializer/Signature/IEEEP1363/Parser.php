<?php
namespace Mdanter\Ecc\Serializer\Signature\IEEEP1363;

use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Exception\SignatureDecodeException;
use Mdanter\Ecc\Util\BinaryString;

/**
 * Class Parser
 * @package Mdanter\Ecc\Serializer\Signature\IEEEP1363
 */
class Parser
{
    /**
     * @param string $binary
     * @return SignatureInterface
     */
    public function parse(string $binary): SignatureInterface
    {
        $total_length = BinaryString::length($binary);
        if (($total_length & 1) !== 0) {
            throw new SignatureDecodeException('IEEE-P1363 signatures must be an even length');
        }
        $piece_len = $total_length >> 1;
        $r = bin2hex(BinaryString::substring($binary, 0, $piece_len));
        $s = bin2hex(BinaryString::substring($binary, $piece_len, $piece_len));

        return new Signature(
            gmp_init($r, 16),
            gmp_init($s, 16)
        );
    }
}
