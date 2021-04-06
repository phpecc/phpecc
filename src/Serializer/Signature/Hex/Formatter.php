<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Signature\Hex;

use Mdanter\Ecc\Crypto\Signature\SignatureInterface;

class Formatter
{
    /**
     * @param SignatureInterface $signature
     * @return string
     */
    public function serialize(SignatureInterface $signature): string
    {
        return str_pad(gmp_strval($signature->getR(), 16), 32, '0', STR_PAD_LEFT) . str_pad(gmp_strval($signature->getS(), 16), 32, '0', STR_PAD_LEFT);
    }
}
