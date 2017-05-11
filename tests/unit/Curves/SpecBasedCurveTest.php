<?php

namespace Mdanter\Ecc\Tests\Curves;

use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\Point\CompressedPointSerializer;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Symfony\Component\Yaml\Yaml;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;

class SpecBasedCurveTest extends AbstractTestCase
{
    const CAUSE_MSG = "message"; // 1
    const CAUSE_R = "r"; // 2
    const CAUSE_S = "s"; // 3
    const CAUSE_Q = "publicKey"; // 4
    /**
     * @return array
     */
    public function getFiles()
    {
        return [
            __DIR__ . '/../../specs/secp-112r1.yml',
            __DIR__ . '/../../specs/secp-192k1.yml',
            __DIR__ . '/../../specs/secp-256k1.yml',
            __DIR__ . '/../../specs/secp-256r1.yml',
            __DIR__ . '/../../specs/secp-384r1.yml',
            __DIR__ . '/../../specs/nist-p192.yml',
            __DIR__ . '/../../specs/nist-p224.yml',
            __DIR__ . '/../../specs/nist-p256.yml',
            __DIR__ . '/../../specs/nist-p384.yml',
            __DIR__ . '/../../specs/nist-p521.yml'
        ];
    }

    /**
     * @return array
     */
    public function getKeypairsTestSet()
    {
        $yaml = new Yaml();
        $files = $this->getFiles();
        $datasets = [];

        foreach ($files as $file) {
            $data = $yaml->parse(file_get_contents($file));
            $generator = CurveFactory::getGeneratorByName($data['name']);

            foreach ($data['keypairs'] as $testKeyPair) {
                $datasets[] = [
                    $data['name'],
                    $generator,
                    $testKeyPair['k'],
                    $testKeyPair['x'],
                    $testKeyPair['y']
                ];
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getKeypairsTestSet()
     * @param GeneratorPoint $generator
     * @param string $k
     * @param string $expectedX
     * @param string $expectedY
     */
    public function testGetPublicKey($name, GeneratorPoint $generator, $k, $expectedX, $expectedY)
    {
        $adapter = $generator->getAdapter();

        $privateKey = $generator->getPrivateKeyFrom(gmp_init($k, 10));
        $publicKey = $privateKey->getPublicKey();

        $this->assertEquals($adapter->hexDec($expectedX), $adapter->toString($publicKey->getPoint()->getX()), $name);
        $this->assertEquals($adapter->hexDec($expectedY), $adapter->toString($publicKey->getPoint()->getY()), $name);

        $serializer = new UncompressedPointSerializer($adapter);
        $serialized = $serializer->serialize($publicKey->getPoint());
        $parsed = $serializer->unserialize($generator->getCurve(), $serialized);
        $this->assertTrue($parsed->equals($publicKey->getPoint()));

        $compressingSerializer = new CompressedPointSerializer($adapter);
        $serialized = $compressingSerializer->serialize($publicKey->getPoint());
        $parsed = $compressingSerializer->unserialize($generator->getCurve(), $serialized);
        $this->assertTrue($parsed->equals($publicKey->getPoint()));
    }

    /**
     * @return array
     */
    public function getDiffieHellmanTestSet()
    {
        $yaml = new Yaml();
        $files = $this->getFiles();
        $datasets = [];

        foreach ($files as $file) {
            $data = $yaml->parse(file_get_contents($file));
            $generator = CurveFactory::getGeneratorByName($data['name']);

            foreach ($data['diffie'] as $testKeyPair) {
                $datasets[] = [
                    $generator,
                    $testKeyPair['alice'],
                    $testKeyPair['bob'],
                    $testKeyPair['shared']
                ];
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getDiffieHellmanTestSet()
     * @param GeneratorPoint $generator
     * @param string $alice
     * @param string $bob
     * @param string $expectedX
     */
    public function testGetDiffieHellmanSharedSecret(GeneratorPoint $generator, $alice, $bob, $expectedX)
    {
        $adapter = $generator->getAdapter();
        $alicePrivKey = $generator->getPrivateKeyFrom(gmp_init($alice, 10));
        $bobPrivKey = $generator->getPrivateKeyFrom(gmp_init($bob, 10));

        $aliceDh = $alicePrivKey->createExchange($bobPrivKey->getPublicKey());
        $bobDh = $bobPrivKey->createExchange($alicePrivKey->getPublicKey());

        $this->assertEquals($adapter->hexDec($expectedX), $adapter->toString($aliceDh->calculateSharedKey()));
        $this->assertEquals($adapter->hexDec($expectedX), $adapter->toString($bobDh->calculateSharedKey()));
    }

    /**
     * @return array
     */
    public function getHmacTestSet()
    {
        $yaml = new Yaml();
        $files = $this->getFiles();
        $datasets = [];

        foreach ($files as $file) {
            $data = $yaml->parse(file_get_contents($file));

            if (! isset($data['hmac'])) {
                continue;
            }

            $generator = CurveFactory::getGeneratorByName($data['name']);

            foreach ($data['hmac'] as $sig) {
                $datasets[] = [
                    $generator,
                    $sig['key'],
                    $sig['algo'],
                    $sig['message'],
                    strtolower($sig['k']),
                    strtolower($sig['r']),
                    strtolower($sig['s'])
                ];
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getHmacTestSet
     * @param GeneratorPoint $G
     * @param string $privKey
     * @param string $algo
     * @param string $message
     * @param string $eK expected K hex
     * @param string $eR expected R hex
     * @param string $eS expected S hex
     */
    public function testHmacSignatures(GeneratorPoint $G, $privKey, $algo, $message, $eK, $eR, $eS)
    {
        $math = $G->getAdapter();

        $privateKey = $G->getPrivateKeyFrom(gmp_init($privKey, 16));
        $signer = new Signer($math);
        $hashDec = $signer->hashData($G, $algo, $message);

        $hmac = RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $hashDec, $algo);
        $k = $hmac->generate($G->getOrder());
        $this->assertEquals($math->hexDec($eK), gmp_strval($k, 10), 'k');

        $sig = $signer->sign($privateKey, $hashDec, $k);
        // Should verify
        $this->assertTrue($signer->verify($privateKey->getPublicKey(), $sig, $hashDec));

        // R and S should be correct
        $sR = $math->hexDec($eR);
        $sS = $math->hexDec($eS);
        $this->assertSame($sR, $math->toString($sig->getR()));
        $this->assertSame($sS, $math->toString($sig->getS()));
    }

    /**
     * @return array
     */
    public function getEcdsaSignFixtures()
    {
        $yaml = new Yaml();
        $files = $this->getFiles();
        $datasets = [];

        foreach ($files as $file) {
            $data = $yaml->parse(file_get_contents($file));
            $generator = CurveFactory::getGeneratorByName($data['name']);

            if (array_key_exists('ecdsa', $data)) {
                foreach ($data['ecdsa'] as $testKeyPair) {
                    $algo = null;
                    $msg = null; // full message, not the digest
                    $hashRaw = null;
                    if (!array_key_exists('msg', $testKeyPair)) {
                        if (!array_key_exists('msg_full', $testKeyPair)) {
                            throw new \RuntimeException("Need full message if not given raw hash value");
                        }
                        if (!array_key_exists('algo', $testKeyPair)) {
                            throw new \RuntimeException("Need algorithm in order to hash message");
                        }
                        $algo = $testKeyPair['algo'];
                        $msg = $testKeyPair['msg_full'];
                    } else {
                        $hashRaw = $testKeyPair['msg'];
                    }

                    $datasets[] = [
                        $generator,
                        $testKeyPair['private'],
                        (string) $testKeyPair['k'],
                        (string) $testKeyPair['r'],
                        (string) $testKeyPair['s'],
                        $hashRaw,
                        $msg,
                        $algo,
                    ];
                }
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getEcdsaSignFixtures
     * @param GeneratorPoint $G
     * @param $privKeyHex
     * @param $hashHex
     * @param $kHex
     * @param $eR
     * @param $eS
     * @param string|null $algo
     */
    public function testEcdsaSignatureGeneration(GeneratorPoint $G, $privKeyHex, $kHex, $eR, $eS, $hashHex = null, $msg = null, $algo = null)
    {
        $math = $G->getAdapter();
        $signer = new Signer($math);
        $privateKey = $G->getPrivateKeyFrom(gmp_init($privKeyHex, 10));

        if ($hashHex != null) {
            $hash = gmp_init($hashHex, 16);
        } else {
            $hash = $signer->hashData($G, $algo, hex2bin($msg));
        }

        $k = gmp_init($kHex, 16);

        $sig = $signer->sign($privateKey, $hash, $k);

        // R and S should be correct
        $sR = $math->hexDec($eR);
        $sS = $math->hexDec($eS);
        $this->assertSame($sR, $math->toString($sig->getR()));
        $this->assertSame($sS, $math->toString($sig->getS()));

        // Should verify
        $this->assertTrue($signer->verify($privateKey->getPublicKey(), $sig, $hash));
    }


    /**
     * @return array
     */
    public function getEcdsaVerifyFixtures()
    {
        $yaml = new Yaml();
        $files = $this->getFiles();
        $datasets = [];

        foreach ($files as $file) {
            $data = $yaml->parse(file_get_contents($file));
            $generator = CurveFactory::getGeneratorByName($data['name']);

            if (array_key_exists('ecdsa-verify', $data)) {
                foreach ($data['ecdsa-verify'] as $testKeyPair) {
                    $algo = null;
                    $msg = null; // full message, not the digest
                    $hashRaw = null;
                    $cause = null;
                    if (!array_key_exists('msg', $testKeyPair)) {
                        if (!array_key_exists('msg_full', $testKeyPair)) {
                            throw new \RuntimeException("Need full message if not given raw hash value");
                        }
                        if (!array_key_exists('algo', $testKeyPair)) {
                            throw new \RuntimeException("Need algorithm in order to hash message");
                        }
                        $algo = $testKeyPair['algo'];
                        $msg = $testKeyPair['msg_full'];
                    } else {
                        $hashRaw = $testKeyPair['msg'];
                    }

                    if (!$testKeyPair['result'] && array_key_exists("cause", $testKeyPair)) {
                        $cause = $testKeyPair["cause"];
                    }

                    $datasets[] = [
                        $generator,
                        (string) $testKeyPair['r'],
                        (string) $testKeyPair['s'],
                        (string) $testKeyPair['x'],
                        (string) $testKeyPair['y'],
                        $testKeyPair['result'],
                        $cause,
                        $hashRaw,
                        $msg,
                        $algo,
                    ];
                }
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getEcdsaVerifyFixtures
     * @param GeneratorPoint $G
     * @param $hashHex
     * @param $eR
     * @param $eS
     * @param $x
     * @param $y
     * @param bool $result
     * @param string $cause
     * @param string|null $algo
     */
    public function testEcdsaSignatureVerification(GeneratorPoint $G, $eR, $eS, $x, $y, $result, $cause = null, $hashHex = null, $msg = null, $algo = null)
    {
        $math = $G->getAdapter();
        $signer = new Signer($math);
        try {
            $publicKey = $G->getPublicKeyFrom(gmp_init($x, 16), gmp_init($y, 16));
        } catch (\Exception $e) {
            throw new \RuntimeException("Unexpected exception parsing public key");
        }

        if ($hashHex != null) {
            $hash = gmp_init($hashHex, 16);
        } else {
            $hash = $signer->hashData($G, $algo, hex2bin($msg));
        }

        $sig = new Signature(gmp_init($eR, 16), gmp_init($eS, 16));

        // Should verify
        $verify = $signer->verify($publicKey, $sig, $hash);
        $this->assertEquals($result, $verify);
    }
}
