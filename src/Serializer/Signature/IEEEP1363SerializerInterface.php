<?php
namespace Mdanter\Ecc\Serializer\Signature;

use Mdanter\Ecc\Crypto\Signature\SignatureInterface;

/**
 * Interface IEEEP1363SerializerInterface
 * @package Mdanter\Ecc\Serializer\Signature
 */
interface IEEEP1363SerializerInterface
{
    /**
     * @param SignatureInterface $signature
     * @return string
     */
    public function serialize(SignatureInterface $signature): string;

    /**
     * @param string $binary
     * @return SignatureInterface
     */
    public function parse(string $binary): SignatureInterface;
}
