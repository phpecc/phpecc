<?php
declare(strict_types=1);
namespace Mdanter\Ecc\Serializer\Signature\IEEEP1363;

use Mdanter\Ecc\Crypto\Signature\SignatureInterface;

class Formatter
{
    /**
     * @param SignatureInterface $signature
     * @return string
     */
    public function serialize(SignatureInterface $signature): string
    {
        $r = gmp_strval($signature->getR(), 16);
        $s = gmp_strval($signature->getS(), 16);
        $len = max(strlen($r), strlen($s));
        return hex2bin(
            str_pad($r, $len, '0', STR_PAD_LEFT) .
            str_pad($s, $len, '0', STR_PAD_LEFT)
        );
    }
}
