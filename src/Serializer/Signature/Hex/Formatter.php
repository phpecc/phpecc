<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Signature\Hex;

use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Primitives\CurveFpInterface;
use Mdanter\Ecc\Exception\SignatureDecodeException;

class Formatter
{
    /**
     * @param SignatureInterface $signature
     * @return string
     * @throws SignatureDecodeException
     */
    public function serialize(SignatureInterface $signature, CurveFpInterface $curve): string
    {
        $hexLength = 2 * (int) ceil($curve->getSize() / 8); // 2 * (bitLen to byteLen)

        $hexR = gmp_strval($signature->getR(), 16);
        $hexS = gmp_strval($signature->getS(), 16);
        if (strlen($hexR) > $hexLength || strlen($hexS) > $hexLength) {
            throw new SignatureDecodeException('Signature length does not match curve');
        }
        return str_pad($hexR, $hexLength, '0', STR_PAD_LEFT) . str_pad($hexS, $hexLength, '0', STR_PAD_LEFT);
    }
}
