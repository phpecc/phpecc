<?php

namespace Mdanter\Ecc\WycheProof;

use Mdanter\Ecc\Crypto\Signature\HasherInterface;
use Mdanter\Ecc\Crypto\Signature\SignHasher;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Curves\NistCurve;

class EcdsaFixtures
{
    private $groups;
    private $hashFunctions = [
        "SHA-224" => "sha224",
        "SHA-256" => "sha256",
        "SHA-384" => "sha384",
        "SHA-512" => "sha512",
    ];
    private $curveAltName = [
        "secp224r1" => NistCurve::NAME_P224,
        "secp256r1" => NistCurve::NAME_P256,
        "secp384r1" => NistCurve::NAME_P384,
        "secp521r1" => NistCurve::NAME_P521,
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

    /**
     * Pass 'supported
     * @param array $limitToCurves
     * @return array
     * @throws \Mdanter\Ecc\Exception\UnsupportedCurveException
     */
    public function makeFixtures(array $limitToCurves = []): array
    {
        $fixtures = [];
        foreach ($this->groups as $group) {
            $curve = $group['key']['curve'];
            if (array_key_exists($curve, $this->curveAltName)) {
                $curve = $this->curveAltName[$curve];
            }

            if (count($limitToCurves) > 0) {
                if (!in_array($curve, $limitToCurves)) {
                    continue;
                }
            }

            $generator = CurveFactory::getGeneratorByName($curve);
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
