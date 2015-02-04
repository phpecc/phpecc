<?php

namespace Mdanter\Ecc\Serializer\PrivateKey;

use Mdanter\Ecc\PrivateKeyInterface;

interface PrivateKeySerializerInterface
{
    /**
     *
     * @param  PrivateKeyInterface $key
     * @return string
     */
    public function serialize(PrivateKeyInterface $key);

    /**
     *
     * @param  string              $formattedKey
     * @return PrivateKeyInterface
     */
    public function parse($formattedKey);
}
