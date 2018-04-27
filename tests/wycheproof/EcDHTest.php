<?php

declare(strict_types=1);

namespace Mdanter\Ecc\WycheProof;

use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Exception\ExchangeException;
use Mdanter\Ecc\Exception\PointNotOnCurveException;
use Mdanter\Ecc\Exception\PointRecoveryException;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Serializer\Point\CompressedPointSerializer;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;

class EcDHTest extends AbstractTestCase
{
    public function getEcDHFixtures(): array
    {
        $curveList = $this->getCurvesList();
        $fixtures = json_decode($this->importFile("import/wycheproof/testvectors/ecdh_test.json"), true);
        $results = [];
        foreach ($fixtures['testGroups'] as $fixture) {
            if (in_array($fixture['curve'], $curveList)) {
                foreach ($fixture['tests'] as $test) {

                    // Library doesn't have all code paths available right now
                    if (!empty(array_intersect(["UnnamedCurve"], $test['flags']))) {
                        continue;
                    }
                    // Library doesn't have all code paths available right now
                    if ($test['public'] === "3052301406072a8648ce3d020106092b2403030208010105033a00046caa3d6d86f792df7b29e41eb4203150f60f4fca10f57d0b2454abfb201f9f7e6dcbb92bdcfb9240dc86bcaeaf157c77bca22b2ec86ee8d6") {
                        continue;
                    }

                    $results[] = [
                        $fixture['curve'],
                        $test['public'],
                        $test['private'],
                        $test['shared'],
                        $test['flags'],
                        $test['result'],
                        $test['comment'],
                        (int) $test['tcId'],
                    ];
                }
            }
        }
        return $results;
    }
    /**
     * @dataProvider getEcDHFixtures
     * @param string $curveName
     * @param string $public
     * @param string $private
     * @param string $shared
     * @param string $result
     * @param string $comment
     */
    public function testEcdh(string $curveName, string $public, string $private, string $shared, array $flags, string $result, string $comment, int $tcId)
    {
        $generator = CurveFactory::getGeneratorByName($curveName);
        $curve = $generator->getCurve();
        $math = new GmpMath();
        if (in_array("CompressedPoint", $flags, true)) {
            $pubKeySerializer = new DerPublicKeySerializer($math, new CompressedPointSerializer($math));
        } else {
            $pubKeySerializer = new DerPublicKeySerializer($math, new UncompressedPointSerializer());
        }

        if ($comment === "public point not on curve") {
            $this->expectException(PointNotOnCurveException::class);
        } else if ($comment === "public point = (0,0)") {
            $this->expectException(PointNotOnCurveException::class);
            $this->expectExceptionMessage("Curve curve({$curve->getA()}, {$curve->getB()}, {$curve->getPrime()}) does not contain point (0, 0)");
        } else if ($comment === "invalid public key" || $comment === "public key is a low order point on twist") {
            $this->expectException(PointRecoveryException::class);
            $this->expectExceptionMessage("Failed to recover y coordinate for point");
        }

        try {
            $pubKey = $pubKeySerializer->parse(hex2bin($public));
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === "Invalid data: unsupported generator.") {
                $this->markTestSkipped("Skipped test case ({$tcId}), no support for generator");
                return;
            }
            throw $e;
        }

        $privateKey = $generator->getPrivateKeyFrom(gmp_init($private, 16));

        $exchange = $privateKey->createExchange($pubKey);

        if ($result === "acceptable" || $result === "valid") {
            $sharedKey = $exchange->calculateSharedKey();
            $hexKey = bin2hex($math->intToFixedSizeString($sharedKey, $curve->getSize() / 8));
            $this->assertEquals($shared, $hexKey, "Test case ({$tcId})");
        } else {
            // This branch should not work, and we have ruled
            // out bad public keys.
            $this->expectException(ExchangeException::class);

            $exchange->calculateSharedKey();
        }
    }
}
