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
    const DEFAULT_COVERAGE_SHARDS = 5;
    /**
     * @var array
     */
    private $fileCache = [];

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
     * @param Yaml $yaml
     * @param $fileName
     * @return mixed
     */
    public function readFile(Yaml $yaml, $fileName)
    {
        if (!isset($this->fileCache[$fileName])) {
            if (!file_exists($fileName)) {
                throw new \PHPUnit_Runner_Exception("Test fixture file {$fileName} does not exist");
            }

            $this->fileCache[$fileName] = $yaml->parse(file_get_contents($fileName));
        }
        return $this->fileCache[$fileName];
    }

    /**
     * @param string $fixtureName
     * @return array
     */
    public function readTestFixture($fixtureName)
    {
        static $useShard;
        $yaml = new Yaml();
        $files = $this->getFiles();

        $shards = 1;
        if (getenv('COVERAGE')) {
            $shards = getenv('COVERAGE_SHARDS');
            if (null === $shards || (int) $shards === 0) {
                $shards = self::DEFAULT_COVERAGE_SHARDS;
            }

            if (null === $useShard) {
                $useShard = mt_rand(0, $shards - 1);
                fwrite(STDERR, <<<TEXT

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
NOTICE:
Sharding enabled for coverage run - testing shard {$useShard} of {$shards}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


TEXT
                );
            }
        }

        $results = [];
        foreach ($files as $fileName) {
            $data = $this->readFile($yaml, $fileName);
            if (!array_key_exists($fixtureName, $data)) {
                continue;
            }

            $filesWorth = [
                'name' => $data['name'],
                'fixtures' => []
            ];

            foreach ($data[$fixtureName] as $i => $fixture) {
                if ($i % $shards != 0) {
                    continue;
                }

                $filesWorth['fixtures'][] = $fixture;
            }

            $results[] = $filesWorth;
        }

        return $results;
    }

    /**
     * @return array
     */
    public function getKeypairsTestSet()
    {
        $files = $this->readTestFixture('keypairs');
        $datasets = [];

        foreach ($files as $file) {
            $generator = CurveFactory::getGeneratorByName($file['name']);
            foreach ($file['fixtures'] as $fixture) {
                $datasets[] = [
                    $file['name'],
                    $generator,
                    $fixture['k'],
                    $fixture['x'],
                    $fixture['y']
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
    public function getPublicKeyVerifyTestSet()
    {
        $files = $this->readTestFixture('pubkey');
        $datasets = [];

        foreach ($files as $file) {
            $generator = CurveFactory::getGeneratorByName($file['name']);
            foreach ($file['fixtures'] as $fixture) {
                $datasets[] = [
                    $file['name'],
                    $generator,
                    $fixture['x'],
                    $fixture['y'],
                    $fixture['result'],
                ];
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getPublicKeyVerifyTestSet
     * @param string $name
     * @param GeneratorPoint $generator
     * @param string $xHex
     * @param string $yHex
     * @param bool $expectedResult
     */
    public function testPublicKeyVerify($name, GeneratorPoint $generator, $xHex, $yHex, $expectedResult)
    {
        $curve = $generator->getCurve();

        $x = gmp_init($xHex, 16);
        $y = gmp_init($yHex, 16);

        // The true test
        try {
            $generator->getPublicKeyFrom($x, $y);
            $fCurveHasPoint = true;
        } catch (\Exception $e) {
            $fCurveHasPoint = false;
        }

        $this->assertEquals($expectedResult, $fCurveHasPoint);

        if ($expectedResult) {
            // CurveFp.contains ...
            // can only check the point exists on the curve after
            // the fields have been checked against the subgroup
            // order. If our fixture is valid, this method MUST
            // return true.
            $fCurveContains = $curve->contains($x, $y);

            // Curve.getPoint must also succeed if the fixture is
            // valid when the order isn't provided.
            try {
                $curve->getPoint($x, $y);
                $fCurveGetPoint = true;
            } catch (\Exception $e) {
                $fCurveGetPoint = false;
            }

            $this->assertEquals(true, $fCurveContains);
            $this->assertEquals(true, $fCurveGetPoint);
        }
    }

    /**
     * @return array
     */
    public function getDiffieHellmanTestSet()
    {
        $files = $this->readTestFixture('diffie');
        $datasets = [];

        foreach ($files as $file) {
            $generator = CurveFactory::getGeneratorByName($file['name']);
            foreach ($file['fixtures'] as $fixture) {
                $datasets[] = [
                    $generator,
                    $fixture['alice'],
                    $fixture['bob'],
                    $fixture['shared']
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
        $files = $this->readTestFixture('hmac');
        $datasets = [];

        foreach ($files as $file) {
            $generator = CurveFactory::getGeneratorByName($file['name']);
            foreach ($file['fixtures'] as $fixture) {
                $datasets[] = [
                    $generator,
                    $fixture['key'],
                    $fixture['algo'],
                    $fixture['message'],
                    strtolower($fixture['k']),
                    strtolower($fixture['r']),
                    strtolower($fixture['s'])
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
        $files = $this->readTestFixture('ecdsa');
        $datasets = [];

        foreach ($files as $file) {
            $generator = CurveFactory::getGeneratorByName($file['name']);
            foreach ($file['fixtures'] as $testKeyPair) {
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
        $files = $this->readTestFixture('ecdsa-verify');
        $datasets = [];

        foreach ($files as $file) {
            $generator = CurveFactory::getGeneratorByName($file['name']);
            foreach ($file['fixtures'] as $testKeyPair) {
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
