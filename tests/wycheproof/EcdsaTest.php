<?php

declare(strict_types=1);

namespace Mdanter\Ecc\WycheProof;

use FG\ASN1\Exception\ParserException;
use Mdanter\Ecc\Crypto\Key\PublicKey;
use Mdanter\Ecc\Crypto\Signature\HasherInterface;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Exception\InvalidSignatureException;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

class EcdsaTest extends AbstractTestCase
{
    private function filterSet(EcdsaFixtures $fixturesSet, array $disabledFlags): array
    {
        $fixtures = [];
        foreach ($fixturesSet->makeFixtures($this->getCurvesList()) as $fixture) {
            if (!empty(array_intersect($fixture[6], $disabledFlags))) {
                continue;
            }
            if ($fixture[8] === "long form encoding of length") {
                continue;
            }
            if ($fixture[8] === "length contains leading 0") {
                continue;
            }
            $fixtures[] = $fixture;
        }
        return $fixtures;
    }

    private function readSpecificSet(string $curveName, string $hasherName): array
    {
        $wycheproof = new WycheproofFixtures(__DIR__ . "/../import/wycheproof");
        $disabledFlags = ["MissingZero"];
        return $this->filterSet($wycheproof->getSpecificEcdsaFixtures($curveName, $hasherName), $disabledFlags);
    }

    public function getEcdsaTestVectors(): array
    {
        $wycheproof = new WycheproofFixtures(__DIR__ . "/../import/wycheproof");
        $disabledFlags = ["MissingZero"];
        return $this->filterSet($wycheproof->getEcdsaFixtures(), $disabledFlags);
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
        } catch (InvalidSignatureException $e) {
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
