<?php

namespace Mdanter\Ecc\Serializer\PublicKey;

use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;

interface PublicKeySerializerInterface
{
    /**
     *
     * @param  PublicKeyInterface $key
     * @return string
     */
    public function serialize(PublicKeyInterface $key);

    /**
     *
     * @param  string $formattedKey
     * @return PublicKeyInterface
     */
    public function parse($formattedKey);
}
