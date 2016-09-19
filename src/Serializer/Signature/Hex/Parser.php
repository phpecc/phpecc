<?php

namespace Mdanter\Ecc\Serializer\Signature\Hex;

use Mdanter\Ecc\Crypto\Signature\Signature;

class Parser
{
    /**
     * @param string $hex
     * @return Signature
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function parse($hex)
    {
        $parts = explode('|', $hex);
        if (count($parts) === 1) {
            if (strlen($hex) !== 128) {
                throw new \RuntimeException('Failed to parse signature');
            }
            $rHex = substr($hex, 0, 64);
            $sHex = substr($hex, 64);
        } else if (count($parts) === 2) {
            $rHex = $parts[0];
            $sHex = $parts[1];
        }
        return new Signature(
            gmp_init($rHex, 16),
            gmp_init($sHex, 16)
        );
    }
}
