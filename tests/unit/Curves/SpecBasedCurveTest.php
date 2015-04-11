<?php

namespace Mdanter\Ecc\Tests\Curves;

use Mdanter\Ecc\Message\MessageFactory;
use Mdanter\Ecc\Tests\AbstractTestCase;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Util\NumberSize;
use Symfony\Component\Yaml\Yaml;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;

class SpecBasedCurveTest extends AbstractTestCase
{

    /**
     * @return array
     */
    public function getFiles()
    {
        return [
            __DIR__ . '/../../specs/secg-256r1.yml',
            __DIR__ . '/../../specs/secg-256k1.yml',
            __DIR__ . '/../../specs/secg-384r1.yml',
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
            $data = $yaml->parse($file);
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

        $privateKey = $generator->getPrivateKeyFrom($k);
        $publicKey = $privateKey->getPublicKey();

        $this->assertEquals($adapter->hexDec($expectedX), $publicKey->getPoint()->getX(), $name);
        $this->assertEquals($adapter->hexDec($expectedY), $publicKey->getPoint()->getY(), $name);
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
            $data = $yaml->parse($file);
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
        $messages = new MessageFactory($adapter);
        $alicePrivKey = $generator->getPrivateKeyFrom($alice);
        $bobPrivKey = $generator->getPrivateKeyFrom($bob);

        $aliceDh = $alicePrivKey->createExchange($messages, $bobPrivKey->getPublicKey());
        $bobDh = $bobPrivKey->createExchange($messages, $alicePrivKey->getPublicKey());

        $this->assertEquals($aliceDh->calculateSharedKey(), $adapter->hexDec($expectedX));
        $this->assertEquals($bobDh->calculateSharedKey(), $adapter->hexDec($expectedX));
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
            $data = $yaml->parse($file);

            if (! isset($data['hmac'])) {
                continue;
            }

            $generator = CurveFactory::getGeneratorByName($data['name']);

            foreach ($data['hmac'] as $sig) {
                $datasets[] = [
                    $generator,
                    isset($sig['size']) ? $sig['size'] : 0,
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
     * @param integer $size
     * @param string $privKey
     * @param string $algo
     * @param string $message
     * @param string $eK expected K hex
     * @param string $eR expected R hex
     * @param string $eS expected S hex
     */
    public function testHmacSignatures(GeneratorPoint $G, $size, $privKey, $algo, $message, $eK, $eR, $eS)
    {
        //echo "Try {$test->curve} / {$test->algorithm} / '{$test->message}'\n";

        $math = $G->getAdapter();
        
        // Initialize private key and message hash (decimal)
        $privateKey  = $G->getPrivateKeyFrom($math->hexDec($privKey));
        $hashHex     = hash($algo, $message);
        $messageHash = $math->hexDec($hashHex);

        // Derive K
        $drbg = RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $messageHash, $algo);
        $k    = $drbg->generate($G->getOrder());
        $this->assertEquals($k, $math->hexdec($eK), 'k');

        $hexSize = strlen($hashHex);
        $hashBits = $math->baseConvert($messageHash, 10, 2);
        if (strlen($hashBits) < $hexSize * 4) {
            $hashBits = str_pad($hashBits, $hexSize * 4, '0', STR_PAD_LEFT);
        }

        $messageHash = $math->baseConvert(substr($hashBits, 0, NumberSize::bnNumBits($math, $G->getOrder())), 2, 10);

        $signer = new Signer($math);
        $sig    = $signer->sign($privateKey, $messageHash, $k);
        // Should be consistent
        $this->assertTrue($signer->verify($privateKey->getPublicKey(), $sig, $messageHash));

        // R and S should be correct
        $sR = $math->hexDec($eR);
        $sS = $math->hexDec($eS);
        $this->assertSame($sR, $sig->getR(), "r $sR == ".$sig->getR());
        $this->assertSame($sS, $sig->getS(), "s $sR == " . $sig->getS());
    }

}