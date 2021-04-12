<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Signature\Hex;

use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Exception\SignatureDecodeException;
use Mdanter\Ecc\Primitives\CurveFpInterface;

class Parser
{
    /**
     * @param string $hex
     * @return Signature
     * @throws SignatureDecodeException
     */
    public function parse(string $hex, CurveFpInterface $curve): SignatureInterface
    {
        if (!preg_match('/^[0-9a-fA-F]+$/', $hex)) {
            throw new SignatureDecodeException('Invalid hex string.');
        }
        $hexLength = 2 * (int) ceil($curve->getSize() / 8); // 2 * (bitLen to byteLen)
        if (strlen($hex) !== 2 * $hexLength) {
            throw new SignatureDecodeException('Invalid data.');
        }
        $rHex = substr($hex, 0, $hexLength);
        $sHex = substr($hex, $hexLength);
        return new Signature(
            gmp_init($rHex, 16),
            gmp_init($sHex, 16)
        );
    }
}
