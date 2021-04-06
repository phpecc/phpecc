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
        $parts = explode('|', $hex);
        if (count($parts) === 1) {
            if (strlen($hex) !== 128) {
                throw new SignatureDecodeException('Invalid data.');
            }
            $rHex = substr($hex, 0, 64);
            $sHex = substr($hex, 64);
        } else if (count($parts) === 2) {
            $rHex = $parts[0];
            $sHex = $parts[1];
        } else {
            throw new SignatureDecodeException('Invalid data.');
        }
        return new Signature(
            gmp_init($rHex, 16),
            gmp_init($sHex, 16)
        );
    }
}
