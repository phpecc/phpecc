<?php

namespace Mdanter\Ecc\Tests\Curves;

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

    /**
     * @return array
     */
    public function getFiles()
    {
        return [
            __DIR__ . '/../../specs/secp-112r1.yml',
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
}
