<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Signature\Hex;

use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Exception\SignatureDecodeException;

class Parser
{
    /**
     * @param string $hex
     * @return Signature
     * @throws SignatureDecodeException
     */
    public function parse(string $hex): SignatureInterface
    {
        if (!preg_match('/^[0-9a-fA-F]+$/', $hex)) {
            throw new SignatureDecodeException('Invalid hex string.');
        }
        $halflen = (int)strlen($hex)/2;
        if (strlen($hex) !== 2 * $halflen) {
            throw new SignatureDecodeException('Invalid data.');
        }
        $rHex = substr($hex, 0, $halflen);
        $sHex = substr($hex, $halflen);
        return new Signature(
            gmp_init($rHex, 16),
            gmp_init($sHex, 16)
        );
    }
}
