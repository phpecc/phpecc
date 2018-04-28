<?php

namespace Mdanter\Ecc\WycheProof;

use Mdanter\Ecc\Crypto\Signature\HasherInterface;
use Mdanter\Ecc\Crypto\Signature\SignHasher;
use Mdanter\Ecc\Curves\CurveFactory;

class EcdsaFixtures
{
    private $groups;
    private $hashFunctions = [
        "SHA-224" => "sha224",
        "SHA-256" => "sha256",
        "SHA-384" => "sha384",
    ];

    public function __construct(array $testGroups)
    {
        $this->groups = $testGroups;
    }

    public function getHasher(string $id): HasherInterface
    {
        if (!array_key_exists($id, $this->hashFunctions)) {
            throw new \RuntimeException("unconfigured hash function: $id");
        }
        return new SignHasher($this->hashFunctions[$id]);
    }

    public function makeFixtures(array $supportedCurves): array
    {
        $fixtures = [];
        foreach ($this->groups as $group) {
            if (!in_array($group['key']['curve'], $supportedCurves)) {
                continue;
            }

            $generator = CurveFactory::getGeneratorByName($group['key']['curve']);
            $publicKey = $generator->getPublicKeyFrom(gmp_init($group['key']['wx'], 16), gmp_init($group['key']['wy'], 16));
            $hasher = $this->getHasher($group['sha']);

            if (!array_key_exists('tests', $group)) {
                throw new \RuntimeException("Missing tests key");
            }
            foreach ($group['tests'] as $test) {
                $fixtures[] = [
                    $generator,
                    $publicKey,
                    $hasher,
                    $test['msg'],
                    $test['sig'],
                    $test['result'],
                    $test['flags'],
                    $test['tcId'],
                    $test['comment'],
                ];
            }
        }
        return $fixtures;
    }
}
