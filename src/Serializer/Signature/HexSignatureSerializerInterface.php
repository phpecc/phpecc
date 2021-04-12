<?php

declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Signature;

use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Exception\SignatureDecodeException;
use Mdanter\Ecc\Primitives\CurveFpInterface;

interface HexSignatureSerializerInterface
{
    /**
     * @param SignatureInterface $signature
     * @return string
     * @throws SignatureDecodeException
     */
    public function serialize(SignatureInterface $signature, CurveFpInterface $curve): string;

    /**
     * @param string $binary
     * @return SignatureInterface
     * @throws SignatureDecodeException
     */
    public function parse(string $hex, CurveFpInterface $curve): SignatureInterface;
}
