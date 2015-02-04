<?php

namespace Mdanter\Ecc\File;

class DerFileLoader implements FileLoader
{
    public function loadPublicKeyData($file)
    {
        return file_get_contents($file);
    }

    public function loadPrivateKeyData($file)
    {
        return file_get_contents($file);
    }
}
