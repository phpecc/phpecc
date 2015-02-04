<?php

namespace Mdanter\Ecc\File;

interface FileLoader
{
    public function loadPublicKeyData($file);

    public function loadPrivateKeyData($file);
}
