<?php

declare(strict_types=1);

namespace Mdanter\Ecc\WycheProof;

use FG\ASN1\Exception\ParserException;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Exception\ExchangeException;
use Mdanter\Ecc\Exception\PointNotOnCurveException;
use Mdanter\Ecc\Exception\PointRecoveryException;
use Mdanter\Ecc\Exception\UnsupportedCurveException;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Serializer\Point\CompressedPointSerializer;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;

class EcdhTest extends AbstractTestCase
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


    public function getEcDHFixtures(): array
    {
        $fixtures = json_decode($this->importFile("import/wycheproof/testvectors/ecdh_test.json"), true);
        return $this->filterFixtures($fixtures, $this->getCurvesList());
    }

    private function filterFixtures(array $fixtures, array $curveList = []): array
    {
        $results = [];
        $math = new GmpMath();
        foreach ($fixtures['testGroups'] as $fixture) {
            $curve = $fixture['curve'];
            if (array_key_exists($curve, $this->curveAltName)) {
                $curve = $this->curveAltName[$curve];
            }

            if (count($curveList) > 0) {
                if (!in_array($curve, $curveList)) {
                    continue;
                }
            }

            foreach ($fixture['tests'] as $test) {
                // Library doesn't have all code paths available right now
                if (!empty(array_intersect(["UnnamedCurve"], $test['flags']))) {
                    continue;
                }

                // Library doesn't have all code paths available right now
                if ($test['public'] === "3052301406072a8648ce3d020106092b2403030208010105033a00046caa3d6d86f792df7b29e41eb4203150f60f4fca10f57d0b2454abfb201f9f7e6dcbb92bdcfb9240dc86bcaeaf157c77bca22b2ec86ee8d6") {
                    continue;
                }

                if (in_array("CompressedPoint", $test['flags'], true)) {
                    $pubKeySerializer = new DerPublicKeySerializer($math, new CompressedPointSerializer($math));
                } else {
                    $pubKeySerializer = new DerPublicKeySerializer($math, new UncompressedPointSerializer());
                }

                try {
                    $pubKeySerializer->parse(hex2bin($test['public']));
                } catch (UnsupportedCurveException $e) {
                    if (in_array($e->getOid(), $this->ignoredCurves)) {
                        continue;
                    }
                } catch (\Exception $e) {
                    // fall through here, probably required in test
                }

                if (false && $test['tcId'] == 120) {
                    return [[
                        $curve,
                        $test['public'],
                        $test['private'],
                        $test['shared'],
                        $test['flags'],
                        $test['result'],
                        $test['comment'],
                        (int) $test['tcId'],
                    ]];
                }

                $results[] = [
                    $curve,
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
        return $results;
    }

    public function getSpecificFixtures(string $curve): array
    {
        $fixtures = json_decode($this->importFile("import/wycheproof/testvectors/ecdh_{$curve}_test.json"), true);
        $filtered =  $this->filterFixtures($fixtures);

        return $filtered;
    }

    /**
     * @dataProvider getEcDHFixtures
     */
    public function testEcdh(string $curveName, string $public, string $private, string $shared, array $flags, string $result, string $comment, int $tcId)
    {
        return $this->doTest($curveName, $public, $private, $shared, $flags, $result, $comment, $tcId);
    }

    public function getSecp224r1Fixtures()
    {
        return $this->getSpecificFixtures("secp224r1");
    }

    /**
     * @dataProvider getSecp224r1Fixtures
     */
    public function testSecp224r1Fixtures(string $curveName, string $public, string $private, string $shared, array $flags, string $result, string $comment, int $tcId)
    {
        return $this->doTest($curveName, $public, $private, $shared, $flags, $result, $comment, $tcId);
    }

    public function getSecp256r1Fixtures()
    {
        return $this->getSpecificFixtures("secp256r1");
    }

    /**
     * @dataProvider getSecp256r1Fixtures
     */
    public function testSecp256r1Fixtures(string $curveName, string $public, string $private, string $shared, array $flags, string $result, string $comment, int $tcId)
    {
        return $this->doTest($curveName, $public, $private, $shared, $flags, $result, $comment, $tcId);
    }

    public function getSecp256k1Fixtures()
    {
        return $this->getSpecificFixtures("secp256k1");
    }

    /**
     * @dataProvider getSecp256k1Fixtures
     */
    public function testSecp256k1Fixtures(string $curveName, string $public, string $private, string $shared, array $flags, string $result, string $comment, int $tcId)
    {
        return $this->doTest($curveName, $public, $private, $shared, $flags, $result, $comment, $tcId);
    }

    public function getSecp384r1Fixtures()
    {
        return $this->getSpecificFixtures("secp384r1");
    }

    /**
     * @dataProvider getSecp384r1Fixtures
     */
    public function testSecp384r1Fixtures(string $curveName, string $public, string $private, string $shared, array $flags, string $result, string $comment, int $tcId)
    {
        return $this->doTest($curveName, $public, $private, $shared, $flags, $result, $comment, $tcId);
    }

    public function getSecp521r1Fixtures()
    {
        return $this->getSpecificFixtures("secp521r1");
    }

    /**
     * @dataProvider getSecp521r1Fixtures
     */
    public function testSecp521r1Fixtures(string $curveName, string $public, string $private, string $shared, array $flags, string $result, string $comment, int $tcId)
    {
        return $this->doTest($curveName, $public, $private, $shared, $flags, $result, $comment, $tcId);
    }

    public function doTest(string $curveName, string $public, string $private, string $shared, array $flags, string $result, string $comment, int $tcId)
    {
        $generator = CurveFactory::getGeneratorByName($curveName);
        $curve = $generator->getCurve();
        $math = new GmpMath();
        if (in_array("CompressedPoint", $flags, true)) {
            $pubKeySerializer = new DerPublicKeySerializer($math, new CompressedPointSerializer($math));
        } else {
            $pubKeySerializer = new DerPublicKeySerializer($math, new UncompressedPointSerializer());
        }

        try {
            $pubKey = $pubKeySerializer->parse(hex2bin($public));
        } catch (PointNotOnCurveException $e) {
            $this->assertTrue($result !== "valid");
            return;
        } catch (ParserException $e) {
            $this->assertTrue($result !== "valid");
            return;
        } catch (PointRecoveryException $e) {
            $this->assertTrue($result === "invalid");
            return;
        } catch (UnsupportedCurveException $e) {
            $this->markTestSkipped("Unsupported curve {$e->getOid()} in test case {$tcId}");
            return;
        } catch (\RuntimeException $e) {
            // asn1 encoding mostly...
            // todo: get a better error for this
            $this->assertTrue($result !== "invalid");
            return;
        } catch (\InvalidArgumentException $e) {
            // uncompressed point serializer, wrong prefix
            $this->assertTrue($result !== "valid");
            return;
        }

        try {
            $privateKey = $generator->getPrivateKeyFrom(gmp_init($private, 16));
            $exchange = $privateKey->createExchange($pubKey);
            $sharedSecret = $exchange->calculateSharedKey();
            $res = bin2hex($math->intToFixedSizeString($sharedSecret, (int)ceil($curve->getSize()/8)));
            if ($result === "invalid") {
                $this->fail("Computed ECDH with invalid parameters");
            }

            $this->assertEquals($shared, $res, "shared secret should be correct");
        } catch (PointNotOnCurveException $e) {
            $this->assertTrue($result === "invalid");
        } catch (ExchangeException $e) {
            $this->assertTrue($result === "invalid");
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
