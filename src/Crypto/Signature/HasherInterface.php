<?php

namespace Mdanter\Ecc\Crypto\Signature;

use Mdanter\Ecc\Primitives\GeneratorPoint;

interface HasherInterface
{
    /**
     * @param string $data
     * @return string
     */
    public function makeRawHash(string $data): string;

    /**
     * @param string $data
     * @param GeneratorPoint $G
     * @return \GMP
     */
    public function makeHash(string $data, GeneratorPoint $G): \GMP;
}
