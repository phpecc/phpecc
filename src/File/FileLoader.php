<?php

namespace Mdanter\Ecc\File;


interface FileLoader
{
    /**
     * @param string $file
     * @return string
     */
    public function loadPublicKeyData($file);

    /**
     * @param string $file
     * @return string
     */
    public function loadPrivateKeyData($file);
}
