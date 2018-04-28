<?php

declare(strict_types=1);

namespace Mdanter\Ecc\WycheProof;

use FG\ASN1\Exception\ParserException;
use Mdanter\Ecc\Crypto\Key\PublicKey;
use Mdanter\Ecc\Crypto\Signature\HasherInterface;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Exception\ExchangeException;
use Mdanter\Ecc\Exception\PointNotOnCurveException;
use Mdanter\Ecc\Exception\PointRecoveryException;
use Mdanter\Ecc\Exception\UnsupportedCurveException;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Serializer\Point\CompressedPointSerializer;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

class ECDSATest extends AbstractTestCase
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
    public function testEcdsa(GeneratorPoint $generator, PublicKey $publicKey, HasherInterface $hasher, string $message, string $sig, string $result, array $flags, string $tcId, string $comment)
    {
        $data = hex2bin($message);
        $hash = $hasher->makeHash($data, $generator);

        $badSigComments = [
            "wrong length",
            'dropping value of integer',
            "Signature with special case values for r and s",
        ];

        if (in_array($comment, $badSigComments)) {
            $this->expectException(ParserException::class);
            $sigSer = new DerSignatureSerializer();
            $sig = $sigSer->parse(hex2bin($sig));
            if ($sig) {
                $this->fail("should have failed parsing sig");
            }
        } else {
            $sigSer = new DerSignatureSerializer();
            $sig = $sigSer->parse(hex2bin($sig));
        }

        $signer = new Signer(new GmpMath());
        $verified = $signer->verify($publicKey, $sig, $hash);
        if ($result === "valid" || $result === "acceptable") {
            $this->assertTrue($verified);
        } else {
            $this->assertFalse($verified);
        }
    }
}
