<?php

declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Signature;

use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Exception\SignatureDecodeException;

interface HexSignatureSerializerInterface
{
    /**
     * @param SignatureInterface $signature
     * @return string
     */
    public function serialize(SignatureInterface $signature): string;

    /**
     * @param string $binary
     * @return SignatureInterface
     * @throws SignatureDecodeException
     */
    public function parse(string $hex): SignatureInterface;
}
