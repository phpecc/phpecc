<?php

namespace Mdanter\Ecc\File;

use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;

interface FileLoader
{
    /**
     * @param $file
     * @return PublicKeyInterface
     */
    public function loadPublicKeyData($file);

    /**
     * @param $file
     * @return PrivateKeyInterface
     */
    public function loadPrivateKeyData($file);
}
