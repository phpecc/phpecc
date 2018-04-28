<?php

namespace Mdanter\Ecc\WycheProof;

class WycheproofFixtures
{
    /**
     * @var string
     */
    private $dir;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    private function getFixturePath(string $file): string
    {
        return "{$this->dir}/testvectors/{$file}";
    }

    public function getEcdsaFixtures()
    {
        $path = $this->getFixturePath("ecdsa_test.json");
        $decoded = json_decode(file_get_contents($path), true);
        return new EcdsaFixtures($decoded['testGroups']);
    }
}
