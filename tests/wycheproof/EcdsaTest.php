<?php

declare(strict_types=1);

namespace Mdanter\Ecc\WycheProof;

use FG\ASN1\Exception\ParserException;
use Mdanter\Ecc\Crypto\Key\PublicKey;
use Mdanter\Ecc\Crypto\Signature\HasherInterface;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Exception\SignatureDecodeException;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

class EcdsaTest extends AbstractTestCase
{
    private function filterSet(array $fixtures, array $limitToCurves, array $disabledFlags): array
    {
        $results = [];
        foreach ($fixtures['testGroups'] as $group) {
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
                if (!empty(array_intersect($test['flags'], $disabledFlags))) {
                    continue;
                }
                if ($test['comment'] === "long form encoding of length") {
                    continue;
                }
                if ($test['comment'] === "length contains leading 0") {
                    continue;
                }
                $results[] = [
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
        return $results;
    }

    private function readSpecificSet(string $curveName, string $hasherName): array
    {
        $fixtures = json_decode($this->importFile("import/wycheproof/testvectors/ecdsa_{$curveName}_{$hasherName}_test.json"), true);
        $disabledFlags = ["MissingZero"];
        return $this->filterSet($fixtures, $this->getCurvesList(), $disabledFlags);
    }

    public function getEcdsaTestVectors(): array
    {
        $fixtures = json_decode($this->importFile("import/wycheproof/testvectors/ecdsa_test.json"), true);
        $disabledFlags = ["MissingZero"];
        return $this->filterSet($fixtures, $this->getCurvesList(), $disabledFlags);
    }

    /**
     * @dataProvider getEcdsaTestVectors
     */
    public function testEcdsa(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sigHex, string $result, array $flags, string $tcId, string $comment)
    {
        return $this->doTest($generator, $publicKey, $hasher, $message, $sigHex, $result, $flags, $tcId, $comment);
    }

    public function getEcdsaSecp224r1Sha224TestVectors(): array
    {
        return $this->readSpecificSet("secp224r1", "sha224");
    }

    /**
     * @dataProvider getEcdsaSecp224r1Sha224TestVectors
     */
    public function testSecp224r1Sha224(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sigHex, string $result, array $flags, string $tcId, string $comment)
    {
        return $this->doTest($generator, $publicKey, $hasher, $message, $sigHex, $result, $flags, $tcId, $comment);
    }

    public function getEcdsaSecp224r1Sha256TestVectors(): array
    {
        return $this->readSpecificSet("secp224r1", "sha256");
    }

    /**
     * @dataProvider getEcdsaSecp224r1Sha256TestVectors
     */
    public function testSecp224r1Sha256(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sigHex, string $result, array $flags, string $tcId, string $comment)
    {
        return $this->doTest($generator, $publicKey, $hasher, $message, $sigHex, $result, $flags, $tcId, $comment);
    }

    public function getEcdsaSecp224r1Sha512TestVectors(): array
    {
        return $this->readSpecificSet("secp224r1", "sha512");
    }

    /**
     * @dataProvider getEcdsaSecp224r1Sha512TestVectors
     */
    public function testSecp224r1Sha512(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sigHex, string $result, array $flags, string $tcId, string $comment)
    {
        return $this->doTest($generator, $publicKey, $hasher, $message, $sigHex, $result, $flags, $tcId, $comment);
    }

    public function getEcdsaSecp256k1Sha256TestVectors(): array
    {
        return $this->readSpecificSet("secp256k1", "sha256");
    }

    /**
     * @dataProvider getEcdsaSecp256k1Sha256TestVectors
     */
    public function testSecp256k1Sha256(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sigHex, string $result, array $flags, string $tcId, string $comment)
    {
        return $this->doTest($generator, $publicKey, $hasher, $message, $sigHex, $result, $flags, $tcId, $comment);
    }

    public function getEcdsaSecp256k1Sha512TestVectors(): array
    {
        return $this->readSpecificSet("secp256k1", "sha512");
    }

    /**
     * @dataProvider getEcdsaSecp256k1Sha512TestVectors
     */
    public function testSecp256k1Sha512(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sigHex, string $result, array $flags, string $tcId, string $comment)
    {
        return $this->doTest($generator, $publicKey, $hasher, $message, $sigHex, $result, $flags, $tcId, $comment);
    }

    public function getEcdsaSecp256r1Sha256TestVectors(): array
    {
        return $this->readSpecificSet("secp256r1", "sha256");
    }
    /**
     * @dataProvider getEcdsaSecp256r1Sha256TestVectors
     */
    public function testSecp256r1Sha256(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sigHex, string $result, array $flags, string $tcId, string $comment)
    {
        return $this->doTest($generator, $publicKey, $hasher, $message, $sigHex, $result, $flags, $tcId, $comment);
    }

    public function getEcdsaSecp256r1Sha512TestVectors(): array
    {
        return $this->readSpecificSet("secp256r1", "sha512");
    }

    /**
     * @dataProvider getEcdsaSecp256r1Sha512TestVectors
     */
    public function testSecp256r1Sha512(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sigHex, string $result, array $flags, string $tcId, string $comment)
    {
        return $this->doTest($generator, $publicKey, $hasher, $message, $sigHex, $result, $flags, $tcId, $comment);
    }

    public function getEcdsaSecp384r1Sha256TestVectors(): array
    {
        return $this->readSpecificSet("secp384r1", "sha384");
    }

    /**
     * @dataProvider getEcdsaSecp384r1Sha256TestVectors
     */
    public function testSecp384r1Sha256(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sigHex, string $result, array $flags, string $tcId, string $comment)
    {
        return $this->doTest($generator, $publicKey, $hasher, $message, $sigHex, $result, $flags, $tcId, $comment);
    }

    public function getEcdsaSecp384r1Sha512TestVectors(): array
    {
        return $this->readSpecificSet("secp384r1", "sha512");
    }

    /**
     * @dataProvider getEcdsaSecp384r1Sha512TestVectors
     */
    public function testSecp384r1Sha512(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sigHex, string $result, array $flags, string $tcId, string $comment)
    {
        return $this->doTest($generator, $publicKey, $hasher, $message, $sigHex, $result, $flags, $tcId, $comment);
    }

    public function getEcdsaSecp521r1Sha512TestVectors(): array
    {
        return $this->readSpecificSet("secp521r1", "sha512");
    }

    /**
     * @dataProvider getEcdsaSecp521r1Sha512TestVectors
     */
    public function testSecp521r1Sha512(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sigHex, string $result, array $flags, string $tcId, string $comment)
    {
        return $this->doTest($generator, $publicKey, $hasher, $message, $sigHex, $result, $flags, $tcId, $comment);
    }

    protected function doTest(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sigHex, string $result, array $flags, string $tcId, string $comment)
    {
        /** @var SignatureInterface|null $sig */
        $sig = null;
        $verified = false;
        $error = false;
        $signer = new Signer($generator->getAdapter());

        try {
            $sigSer = new DerSignatureSerializer();
            $sig = $sigSer->parse(hex2bin($sigHex));
            $hash = $hasher->makeHash(hex2bin($message), $generator);
            $verified = $signer->verify($publicKey, $sig, $hash);
        } catch (SignatureDecodeException $e) {
            $verified = false;
        } catch (ParserException $e) {
            $verified = false;
        } catch (\Exception $e) {
            $error = $e;
        }

        if ($error) {
            throw $error;
        }

        if (!$verified && $result === 'valid') {
            $this->fail("Signature not verified");
        } else if ($verified && $result === "invalid") {
            $this->fail("Signature verified");
        }
        $this->assertEquals($result !== 'invalid', $verified);
    }
}
