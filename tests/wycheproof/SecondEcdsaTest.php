<?php

declare(strict_types=1);

namespace Mdanter\Ecc\WycheProof;

use FG\ASN1\Exception\ParserException;
use Mdanter\Ecc\Crypto\Key\PublicKey;
use Mdanter\Ecc\Crypto\Signature\HasherInterface;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Exception\InvalidSignatureException;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

class SecondEcdsaTest extends AbstractTestCase
{
    private $ignoredCurves = [

        // brainpoolPXXXr1 curves
        '1.3.36.3.3.2.8.1.1.1',
        '1.3.36.3.3.2.8.1.1.3',
        '1.3.36.3.3.2.8.1.1.5',
        '1.3.36.3.3.2.8.1.1.7',
        '1.3.36.3.3.2.8.1.1.9',
        '1.3.36.3.3.2.8.1.1.11',
        '1.3.36.3.3.2.8.1.1.13',

        // brainpoolPXXXt1 curves
        '1.3.36.3.3.2.8.1.1.6',
        '1.3.36.3.3.2.8.1.1.8',
        '1.3.36.3.3.2.8.1.1.10',
        '1.3.36.3.3.2.8.1.1.12',
        '1.3.36.3.3.2.8.1.1.14',
    ];

    public function getEcdsaVerifyFixtures(): array
    {
        $curveList = $this->getCurvesList();
        $wycheproof = new WycheproofFixtures(__DIR__ . "/../import/wycheproof");
        $fixtures = [];
        $disabledFlags = ["MissingZero"];
        foreach ($wycheproof->getEcdsaFixtures()->makeFixtures($curveList) as $fixture) {
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

    /**
     * @dataProvider getEcdsaVerifyFixtures
     * @param string $curveName
     * @param string $public
     * @param string $private
     * @param string $shared
     * @param string $result
     * @param string $comment
     */
    public function testEcdsa(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sigHex, string $result, array $flags, string $tcId, string $comment)
    {
        $badSigComments = [
            "length = 2**64 - 1",
            "length = 2**40 - 1",
            "length = 2**32 - 1",
            "length = 2**31 - 1",
            "incorrect length",
            "indefinite length without termination",
            "removing sequence",
            "appending 0's to sequence",
            "prepending 0's to sequence",
            "appending unused 0's",
            "appending null value",
            "wrong length",
            "uint64 overflow in length",
            "uint32 overflow in length",
            "wrong length",
            'dropping value of integer',
            "Signature with special case values for r and s",
        ];

        /** @var SignatureInterface|null $sig */
        $sig = null;
        $verified = false;
        $error = false;
        $signer = new Signer($generator->getAdapter());

        try {
            $sigSer = new DerSignatureSerializer();
            $sig = $sigSer->parse(hex2bin($sigHex));
            $hash = $hasher->makeHash(hex2bin($message), $generator);
            echo "Testing ECDSA verification\n";
            print_r($sig);
            $verified = $signer->verify($publicKey, $sig, $hash);
        } catch (InvalidSignatureException $e) {
            echo get_class($e).PHP_EOL;
            $verified = false;
        } catch (ParserException $e) {
            echo get_class($e).PHP_EOL;
            $verified = false;
        } catch (\Exception $e) {
            echo get_class($e).PHP_EOL;
            $error = $e;
        }

        if ($error) {
            throw $error;
        }

        if (!$verified && $result === 'valid') {
            $this->fail("Signature not verified");
        } else if ($verified && $result === "invalid") {
            echo "sigHex: $sigHex\n";

            $this->fail("Signature verified");
        }
        $this->assertTrue(true);
    }
}
