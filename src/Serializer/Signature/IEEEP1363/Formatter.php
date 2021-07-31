<?php
declare(strict_types=1);
namespace Mdanter\Ecc\Serializer\Signature\IEEEP1363;

use Mdanter\Ecc\Crypto\Signature\SignatureInterface;

class Formatter
{
    /**
     * @param SignatureInterface $signature
     * @param int $curveSize
     * @return string
     */
    public function serialize(SignatureInterface $signature, int $curveSize = 0): string
    {
        $r = gmp_strval($signature->getR(), 16);
        $s = gmp_strval($signature->getS(), 16);
        if ($curveSize) {
            // Round up to nearest byte size, then double (for hex)
            $len = (($curveSize + 7) >> 3) << 1;
        } else {
            // Attempt to determine this from $r and $s
            $len = max(strlen($r), strlen($s));
        }
        return hex2bin(
            str_pad($r, $len, '0', STR_PAD_LEFT) .
            str_pad($s, $len, '0', STR_PAD_LEFT)
        );
    }
}
