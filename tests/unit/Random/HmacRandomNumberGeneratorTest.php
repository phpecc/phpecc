<?php

namespace Mdanter\Ecc\Tests\Random;

use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Key\PrivateKey;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Tests\AbstractTestCase;
use Mdanter\Ecc\Util\NumberSize;

class HmacRandomNumberGeneratorTest extends AbstractTestCase
{
    /**
     * @var \Mdanter\Ecc\Math\MathAdapterInterface
     */
    protected $math;
    /**
     * @var \Mdanter\Ecc\Primitives\GeneratorPoint
     */
    protected $G;

    /**
     *
     */
    public function setUp()
    {
        $this->math = EccFactory::getAdapter();
        $this->G = EccFactory::getSecgCurves()->generator256k1();
    }

    public function testCurve()
    {
        $math = EccFactory::getAdapter();
        $G = CurveFactory::getGeneratorByName("nist-p224");
        $algo = 'sha256';

        // Initialize private key and message hash (decimal)
        $privateKey  = $G->getPrivateKeyFrom($this->math->hexDec('F220266E1105BFE3083E03EC7A3A654651F45E37167E88600BF257C1'));
        $messageHash = $this->math->hexDec(hash($algo, "sample"));

        // Derive K
        $drbg = RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $messageHash, $algo);
        $k    = $drbg->generate($this->G->getOrder());
        //$this->assertEquals($this->math->hexdec($test->expectedK), $k);

        $signer = new Signer($this->math);
        $sig    = $signer->sign($privateKey, $messageHash, $k);

        // R and S should be correct
        //$sR = $this->math->hexDec(substr(strtolower($test->expectedRS), 0, 64));
        //$sS = $this->math->hexDec(substr(strtolower($test->expectedRS), 64, 64));

        //$this->assertSame($sR, $sig->getR());
        //$this->assertSame($sS, $sig->getS());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage HMACDRGB: Hashing algorithm not found
     */
    public function testRequireValidAlgorithm()
    {
        $privateKey  = new PrivateKey($this->math, $this->G, 1);
        $hash = hash('sha256', 'message');

        RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $hash, 'sha256a');
    }

    public function atestDeterministicSign()
    {
        $f = file_get_contents(__DIR__.'/../../data/rfc6979.json');
        $json = json_decode($f);
        $math = $this->math;
        $G = $this->G;

        foreach ($json->test as $test) {
            $G = CurveFactory::getGeneratorByName($test->curve);

            // Initialize private key and message hash (decimal)
            $privateKey  = new PrivateKey($math, $G, $math->hexDec($test->privKey));
            $messageHash = $this->math->hexDec(hash($test->algorithm, $test->message));


            // Derive K
            $drbg = RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $messageHash, $test->algorithm);

            // K must be correct (from privatekey and message hash)
            $k    = $drbg->generate($G->getOrder());
            $this->assertEquals(strtolower($test->expectedK), $math->decHex($k));

            $hashBits = $this->math->baseConvert($messageHash, 10, 2);
            $size = NumberSize::bnNumBits($this->math, $messageHash);
            $messageHash = $this->math->baseConvert(substr($hashBits, 0, $size), 2, 10);
            $signer = new Signer($math);
            $sig    = $signer->sign($privateKey, $messageHash, $k);

            // R and S should be correct
            $sR = $this->math->hexDec($test->expectedR);
            $sS = $this->math->hexDec($test->expectedS);

            $this->assertTrue($signer->verify($privateKey->getPublicKey(), $sig, $messageHash));

            $this->assertSame($sR, $sig->getR(), 'r');
            $this->assertSame($sS, $sig->getS(), 's');
        }
    }

    public function getDeterministicSign2Data()
    {
        $data = [];

        $f = file_get_contents(__DIR__.'/../../data/rfc6979.2.json');
        $json = json_decode($f);

        foreach ($json->test as $test) {
            $data[] = [
                $test->curve,
                isset($test->size) ? $test->size : 0,
                $test->algorithm,
                $test->privKey,
                $test->message,
                $test->expectedK,
                $test->expectedR,
                $test->expectedS
            ];
        }

        return $data;
    }

    /**
     * @dataProvider getDeterministicSign2Data
     */
    public function testDeterministicSign2($curve, $size, $algo, $privKey, $message, $eK, $eR, $eS)
    {
        //echo "Try {$test->curve} / {$test->algorithm} / '{$test->message}'\n";
        $G = CurveFactory::getGeneratorByName($curve);

        // Initialize private key and message hash (decimal)
        $privateKey  = $G->getPrivateKeyFrom($this->math->hexDec($privKey));
        $hashHex     = hash($algo, $message);
        $messageHash = $this->math->hexDec($hashHex);

        // Derive K
        $drbg = RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $messageHash, $algo);
        $k    = $drbg->generate($G->getOrder());
        $this->assertEquals($this->math->hexdec($eK), $k, 'k');

        $hexSize = strlen($hashHex);
        $hashBits = $this->math->baseConvert($messageHash, 10, 2);

        if (strlen($hashBits) < $hexSize * 4) {
            $hashBits = str_pad($hashBits, $hexSize * 4, '0', STR_PAD_LEFT);
        }

        $messageHash = $this->math->baseConvert(substr($hashBits, 0, $size), 2, 10);

        $signer = new Signer($this->math);
        $sig    = $signer->sign($privateKey, $messageHash, $k);

        // R and S should be correct
        $sR = $this->math->hexDec($eR);
        $sS = $this->math->hexDec($eS);

        $this->assertTrue($signer->verify($privateKey->getPublicKey(), $sig, $messageHash));
        $this->assertSame($sR, $sig->getR(), 'r');
        $this->assertSame($sS, $sig->getS(), 's');
    }
}
