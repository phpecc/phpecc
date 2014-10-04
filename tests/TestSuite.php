<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\CurveFp;
use Mdanter\Ecc\EcDH;
use Mdanter\Ecc\NumberTheory;
use Mdanter\Ecc\Point;
use Mdanter\Ecc\Points;
use Mdanter\Ecc\PrivateKey;
use Mdanter\Ecc\PublicKey;
use Mdanter\Ecc\Signature;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Curves\SecCurve;
use Mdanter\Ecc\Math\BcMath;
use Mdanter\Ecc\Math\BcMathUtils;
use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Math\GmpUtils;
use Mdanter\Ecc\EccFactory;

/**
 * *********************************************************************
 * Copyright (C) 2012 Matyas Danter
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES
 * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 * ***********************************************************************
 */

/**
 * This suite tests point validity, x9.x.x etc.
 * for EC, ECDSA,ECDH
 * This class also demonstrates how to use the EC classes.
 *
 * @author Matej Danter
 */
class TestSuite
{

    private $START_PRIME = 31;

    private $NUM_PRIMES = 10;

    private static $useGmp = false;

    private static $errors = 0;

    private $verbose = false;

    public function __construct($verbose = false)
    {
        $this->verbose = $verbose;
    }

    public function run($useGmp) {
        self::$useGmp = $useGmp;
        self::$errors = 0;

        $verbose = $this->verbose;

        return;

        if (self::$useGmp && extension_loaded('gmp')) {
            $start_time = microtime(true);
            echo "GMP PHP extension was found and is preferred for performance reasons.<br /><b>Initiating tests.</b><br />\n";
            if (! $verbose) {
                echo "You selected NON-VERBOSE mode. <br /><b>ONLY FAILURES and TIME STATISTICS are REPORTED.</b><br />\n";
            } else {
                echo "You selected VERBOSE mode. <br /><b>ALL OUTCOMES are REPORTED.</b><br />\n";
            }

            echo "<br /><br />--------- START NEXT PRIME TEST ---------<br /><br />\n";
            echo "Test ported to unit tests<br />\n";
            echo "<br /><br />--------- END NEXT PRIME TEST ---------<br /><br />\n";
            echo "--------- START SQUARE ROOT MOD P TEST ---------<br /><br />\n";
            echo "Test ported to unit tests<br />\n";
            echo "<br /><br />--------- END SQUARE ROOT MOD P TEST ---------<br /><br />\n";
            echo "--------- START MULTIPLICATIVE INVERSE MOD P TEST ---------<br /><br />\n";
            echo "Test ported to unit tests<br />\n";
            echo "<br /><br />--------- END MULTIPLICATIVE INVERSE TEST ---------<br /><br />\n";
            echo "--------- START ELLIPTIC CURVE ARITHMETIC TEST ---------<br /><br />\n";
            echo "Test ported to unit tests<br />\n";
            echo "<br /><br />--------- END ELLIPTIC CURVE ARITHMETIC TEST ---------<br /><br />\n";
            echo "--------- START NIST PUBLISHED CURVES TEST ---------<br /><br />\n";

            self::gmp_NISTCurveTest($verbose);
            echo "<br /><br />--------- END NIST PUBLISHED CURVES TEST ---------<br /><br />\n";
            echo "--------- START POINT VALIDITY TEST ---------<br /><br />\n";
            self::gmp_pointValidity($verbose);
            echo "<br /><br />--------- END POINT VALIDITY TEST ---------<br /><br />\n";
            echo "--------- START SIGNATURE VALIDITY TEST ---------<br /><br />\n";
            self::gmp_signatureValidity($verbose);
            echo "<br /><br />--------- END SIGNATURE VALIDITY TEST ---------<br /><br />\n";
            echo "--------- START DIFFIE HELLMAN KEY EXCHANGE TEST ---------<br /><br />\n";
            self::gmp_diffieHellman($verbose);
            echo "<br /><br />--------- END DIFFIE HELLMAN KEY EXHANGE TEST ---------<br /><br />\n";
            self::test_SECGcurve();
            echo "<br /><br />--------- END SECCURVE TEST ---------<br /><br />\n";
            $end_time = microtime(true);

            $time_res = $end_time - $start_time;

            echo "<br /><h3>TEST SUITE TOTAL TIME : " . $time_res . " seconds. </h3><br />";
        } else
        if (extension_loaded('bcmath')) {
            $start_time = microtime(true);
            echo "<b>BCMATH PHP extension</b> was found performance will tend to <b>SEVERELY LACK USABILITY</b>. Consider installing GMP. <br /><b>Initiating tests.</b><br />\n";
            if (! $verbose) {
                echo "You selected NON-VERBOSE mode. <br /><b>ONLY FAILURES and TIME STATISTICS are REPORTED.</b><br />\n";
            } else {
                echo "You selected VERBOSE mode. <br /><b>ALL OUTCOMES are REPORTED.</b><br />\n";
            }
            echo "<br /><br />--------- START NEXT PRIME TEST ---------<br /><br />\n";

            self::bcmath_NextPrime($this->START_PRIME, $this->NUM_PRIMES, $verbose);
            echo "<br /><br />--------- END NEXT PRIME TEST ---------<br /><br />\n";
            echo "--------- START SQUARE ROOT MOD P TEST ---------<br /><br />\n";

            self::bcmath_squareRootModP($this->START_PRIME, $verbose);
            echo "<br /><br />--------- END SQUARE ROOT MOD P TEST ---------<br /><br />\n";
            echo "--------- START MULTIPLICATIVE INVERSE MOD P TEST ---------<br /><br />\n";

            self::bcmath_multInverseModP($verbose);
            echo "<br /><br />--------- END MULTIPLICATIVE INVERSE TEST ---------<br /><br />\n";
            echo "--------- START ELLIPTIC CURVE ARITHMETIC TEST ---------<br /><br />\n";
            echo "Test ported to unit tests<br />\n";
            echo "<br /><br />--------- END ELLIPTIC CURVE ARITHMETIC TEST ---------<br /><br />\n";
            echo "--------- START NIST PUBLISHED CURVES TEST ---------<br /><br />\n";

            self::bcmath_NISTCurveTest($verbose);
            echo "<br /><br />--------- END NIST PUBLISHED CURVES TEST ---------<br /><br />\n";
            echo "--------- START POINT VALIDITY TEST ---------<br /><br />\n";
            self::bcmath_pointValidity($verbose);
            echo "<br /><br />--------- END POINT VALIDITY TEST ---------<br /><br />\n";
            echo "--------- START SIGNATURE VALIDITY TEST ---------<br /><br />\n";
            self::bcmath_signatureValidity($verbose);
            echo "<br /><br />--------- END SIGNATURE VALIDITY TEST ---------<br /><br />\n";
            echo "--------- START DIFFIE HELLMAN KEY EXCHANGE TEST ---------<br /><br />\n";
            self::bcmath_diffieHellman($verbose);
            echo "<br /><br />--------- END DIFFIE HELLMAN KEY EXHANGE TEST ---------<br /><br />\n";
            self::test_SECGcurve();
            echo "<br /><br />--------- END SECCURVE TEST ---------<br /><br />\n";
            $end_time = microtime(true);

            $time_res = $end_time - $start_time;

            echo "<br /><h3>TEST SUITE TOTAL TIME : " . $time_res . " seconds. </h3><br />";
        } else {
            echo "Please install GMP or BCMATH. For higher performance GMP is preferred.";
            self::$errors++;
        }

        return self::$errors;
    }

    public static function gmp_signatureValidity($verbose = false)
    {

        if ($verbose)
            print "Testing the example code:<br />";

            // Building a public/private key pair from the NIST Curve P-192:

        $g = EccFactory::getNistCurves()->generator192();
        $n = $g->getOrder();

        $secret = GmpUtils::gmpRandom($n);

        $secretG = Point::mul($secret, $g);

        $pubkey = new PublicKey($g, Point::mul($secret, $g));

        $privkey = new PrivateKey($pubkey, $secret);

        // Signing a hash value:

        $hash = GmpUtils::gmpRandom($n);

        $signature = $privkey->sign($hash, GmpUtils::gmpRandom($n));

        // Verifying a signature for a hash value:

        if ($pubkey->verifies($hash, $signature)) {
            if ($verbose)
                print "Demo verification succeeded.<br />";
        } else {
            self::$errors++;
            print "*** Demo verification failed.<br />";
        }

        if ($pubkey->verifies(gmp_strval(gmp_sub($hash, 1)), $signature)) {
            self::$errors++;
            print "**** Demo verification failed to reject tampered hash.<br />";
        }
        else {
            if ($verbose)
                print "Demo verification correctly rejected tampered hash.<br />";
        }

        $end_time = microtime(true);

        $time_res = $end_time - $start_time;

        echo "<br />Signing and verification tests from ECDSAVS.pdf B.2.4 took: " . $time_res . " seconds. <br />";
    }

    public static function gmp_diffieHellman($verbose = false)
    {
        $start_time = microtime(true);
        $g = EccFactory::getNistCurves()->generator192();
        $alice = new EcDH($g, new Gmp());

        $bob = new EcDH($g, new Gmp());

        $pubPointA = $alice->getPublicPoint();
        $pubPointB = $bob->getPublicPoint();

        $alice->setPublicPoint($pubPointB);
        $bob->setPublicPoint($pubPointA);

        $key_A = $alice->calculateKey();
        $key_B = $bob->calculateKey();

        if ($key_A == $key_B) {
            if ($verbose)
                echo "<br />ECDH key agreement SUCCESS.";
        } else {
            self::$errors++;
            echo "<br />ECDH key agreement ERROR.";
        }

        $end_time = microtime(true);

        $time_res = $end_time - $start_time;

        echo "<br />Diffie Hellman Dual Key Agreement encryption took: " . $time_res . " seconds. <br />";
    }

    // bcmath test methods
    public static function bcmath_NextPrime($prime, $num_primes, $verbose = false)
    {
        $start_time = microtime(true);

        $nextPrime = NumberTheory::nextPrime($prime);

        $error_tally = 0;

        $cur_prime = $nextPrime;

        for ($i = 0; $i < $num_primes; $i ++) {

            $cur_prime = NumberTheory::nextPrime($cur_prime);

            if (NumberTheory::isPrime($cur_prime)) {
                if ($verbose)
                    echo "SUCCESSFULLY FOUND A LARGE PRIME: " . $cur_prime . "<br />\n";
                flush();
            } else {
                self::$errors++;
                echo "FAILED TO FIND A LARGE PRIME " . $cur_prime . "<br />\n";
                flush();
            }
        }

        $end_time = microtime(true);

        $time_res = $end_time - $start_time;

        echo "<br />Next prime took: " . $time_res . " seconds. <br />";
        flush();
    }

    public static function bcmath_squareRootModP($prime, $verbose = false)
    {
        $start_time = microtime(true);
        if ($verbose)
            echo "Testing primes for modulus " . $prime . "<br />";
        flush();
        $squares = array();

        for ($root = 0; bccomp($root, bcadd(1, bcdiv($prime, 2))) < 0; $root = bcadd($root, 1)) {
            $sq = bcpowmod($root, 2, $prime);

            $calculated = NumberTheory::squareRootModPrime($sq, $prime);

            $calc_sq = bcpowmod($calculated, 2, $prime);

            if (bccomp($calculated, $root) != 0 && bccomp(bcsub($prime, $calculated), $root) != 0) {
                self::$errors++;
                $error_tally ++;
                echo "FAILED TO FIND " . $root . " AS sqrt(" . $sq . ") mod $prime . Said $calculated (" . ($prime - $calculated) . ") <br />\n";

                flush();
            } else {
                if ($verbose)
                    echo "SUCCESS TO FIND " . $root . " AS sqrt(" . $sq . ") mod $prime . Said $calculated (" . ($prime - $calculated) . ") <br />\n";

                flush();
            }
        }
        $end_time = microtime(true);

        $time_res = $end_time - $start_time;

        echo "<br />Square roots mod " . $prime . " took: " . $time_res . " seconds. <br />";
        flush();
    }

    public static function bcmath_multInverseModP($verbose = false)
    {
        $start_time = microtime(true);
        $n_tests = 0;
        for ($i = 0; $i < 100; $i ++) {
            $m = rand(20, 10000);
            for ($j = 0; $j < 100; $j ++) {
                $a = rand(1, $m - 1);
                if (NumberTheory::gcd2($a, $m) == 1) {
                    $n_tests ++;
                    $inv = NumberTheory::inverseMod($a, $m);

                    if ($inv <= 0 || $inv >= $m || ($a * $inv) % $m != 1) {
                        $error_tally ++;
                        self::$errors++;
                        print "$inv = inverseMod( $a, $m ) is wrong.<br />\n";
                        flush();
                    } else {
                        if ($verbose)
                            print "$inv = inverseMod( $a, $m ) is CORRECT.<br />\n";
                        flush();
                    }
                }
            }
        }
        $end_time = microtime(true);

        $time_res = $end_time - $start_time;

        echo "<br />Multiplicative inverse mod arbitrary primes took: " . $time_res . " seconds. <br />";
        flush();
    }

    public static function bcmath_signatureValidity($verbose = false)
    {
        $start_time = microtime(true);
        $p192 = EccFactory::getNistCurves()->generator192();
        $d = '651056770906015076056810763456358567190100156695615665659';
        $k = '6140507067065001063065065565667405560006161556565665656654';
        $e = '968236873715988614170569073515315707566766479517';

        $pubk = new PublicKey($p192, Point::rmul($p192, $d));
        $privk = new PrivateKey($pubk, $d);
        $sig = $privk->sign($e, $k);
        $r = $sig->getR();
        $s = $sig->getS();
        if ($r != '3342403536405981729393488334694600415596881826869351677613' || $s != '5735822328888155254683894997897571951568553642892029982342') {
            self::$errors++;
            print "*** r or s came out wrong.<br />";
            flush();
        } else {
            if ($verbose)
                print "r and s came out right.<br />";
            flush();
        }

        $valid = $pubk->verifies($e, $sig);
        if ($valid) {
            if ($verbose)
                print "Signature verified OK.<br />";
            flush();
        } else {
            self::$errors++;
            print "*** Signature failed verification.<br />";
            flush();
        }
        $valid = $pubk->verifies(bcsub($e, 1), $sig);
        if (! $valid) {
            if ($verbose)
                print "Forgery was correctly rejected.<br />";
            flush();
        } else
            self::$errors++;
            print "*** Forgery was erroneously accepted.<br />";
        flush();

        if ($verbose)
            print "Testing the example code:<br />";
        flush();
        // Building a public/private key pair from the NIST Curve P-192:

        $g = EccFactory::getNistCurves()->generator192();
        $n = $g->getOrder();

        $secret = BcMathUtils::bcrand($n);

        $secretG = Point::mul($secret, $g);

        $pubkey = new PublicKey($g, Point::mul($secret, $g));

        $privkey = new PrivateKey($pubkey, $secret);

        // Signing a hash value:

        $hash = BcMathUtils::bcrand($n);

        $signature = $privkey->sign($hash, BcMathUtils::bcrand($n));

        // Verifying a signature for a hash value:

        if ($pubkey->verifies($hash, $signature)) {
            if ($verbose)
                print "Demo verification succeeded.<br />";
            flush();
        } else {
            self::$errors++;
            print "*** Demo verification failed.<br />";
            flush();
        }

        if ($pubkey->verifies(bcsub($hash, 1), $signature)) {
            self::$errors++;
            print "**** Demo verification failed to reject tampered hash.<br />";
            flush();
        } else {
            if ($verbose)
                print "Demo verification correctly rejected tampered hash.<br />";
            flush();
        }

        $end_time = microtime(true);

        $time_res = $end_time - $start_time;

        echo "<br />Signing and verification tests from ECDSAVS.pdf B.2.4 took: " . $time_res . " seconds. <br />";
        flush();
    }

    public static function bcmath_diffieHellman($verbose = false)
    {
        $start_time = microtime(true);
        $g = EccFactory::getNistCurves()->generator192();
        $alice = new EcDH($g, new BcMath());

        $bob = new EcDH($g, new BcMath());

        $pubPointA = $alice->getPublicPoint();
        $pubPointB = $bob->getPublicPoint();

        $alice->setPublicPoint($pubPointB);
        $bob->setPublicPoint($pubPointA);

        $key_A = $alice->calculateKey();
        $key_B = $bob->calculateKey();

        if ($key_A == $key_B && ! is_null($key_A)) {
            if ($verbose)
                echo "<br />ECDH key agreement SUCCESS.";
            flush();
        } else {
            if (is_null($key_A) && is_null($key_B)) {
                echo "<br />ECDH key agreement ERROR. One of the keys is null.";
                flush();
            } else {
                echo "<br />ECDH key agreement ERROR.";
                flush();
            }
            self::$errors++;
        }

        $end_time = microtime(true);

        $time_res = $end_time - $start_time;

        echo "<br />Diffie Hellman Dual Key Agreement encryption took: " . $time_res . " seconds. <br />";
        flush();
    }

    public static function test_SECGcurve() {
        $tests = array(
            'curve256k1' => array('0', '7', '115792089237316195423570985008687907853269984665640564039457584007908834671663'),
            'curve256r1' => array('115792089210356248762697446949407573530086143415290314195533631308867097853948', '41058363725152142129326129780047268409114441015993725554835256314039467401291', '115792089210356248762697446949407573530086143415290314195533631308867097853951'),
            'curve384r1' => array('39402006196394479212279040100143613805079739270465446667948293404245721771496870329047266088258938001861606973112316', '27580193559959705877849011840389048093056905856361568521428707301988689241309860865136260764883745107765439761230575', '39402006196394479212279040100143613805079739270465446667948293404245721771496870329047266088258938001861606973112319'),
        );

        foreach ($tests as $fn => $result) {
            $obj = call_user_func(array('\\Mdanter\\Ecc\\SECGcurve', $fn));

            if (!$obj || !($obj instanceof CurveFp)) {
                self::$errors++;
                print "*** SecCurve::{$fn} test failed: got object " . (!$obj ? null : get_class($obj)) . ", expected instance of CurveFp.<br />";
                continue;
            }

            if ($obj->getA() != $result[0]) {
                self::$errors++;
                print "*** SecCurve::{$fn}->getA() test failed: got " . $obj->getA() . ", expected " . $result[0] . ".<br />";
            }

            if ($obj->getB() != $result[1]) {
                self::$errors++;
                print "*** SecCurve::{$fn}->getB() test failed: got " . $obj->getB() . ", expected " . $result[1] . ".<br />";
            }

            if ($obj->getPrime() != $result[2]) {
                self::$errors++;
                print "*** SecCurve::{$fn}->getPrime() test failed: got " . $obj->getPrime() . ", expected " . $result[2] . ".<br />";
            }
        }

        $tests = array(
            'generator256k1' => array('115792089237316195423570985008687907852837564279074904382605163141518161494337', '115792089237316195423570985008687907853269984665640564039457584007908834671663'),
            'generator256r1' => array('115792089210356248762697446949407573529996955224135760342422259061068512044369', '115792089210356248762697446949407573530086143415290314195533631308867097853951'),
            'generator384r1' => array('39402006196394479212279040100143613805079739270465446667946905279627659399113263569398956308152294913554433653942643', '39402006196394479212279040100143613805079739270465446667948293404245721771496870329047266088258938001861606973112319'),
        );

        foreach ($tests as $fn => $result) {
            $obj = call_user_func(array('\\Mdanter\\Ecc\\SECGcurve', $fn));

            if (!$obj || !($obj instanceof Point)) {
                self::$errors++;
                print "*** SecCurve::{$fn} test failed: got object " . (!$obj ? null : get_class($obj)) . ", expected instance of Point.<br />";
                continue;
            }

            if ($obj->getOrder() != $result[0]) {
                self::$errors++;
                print "*** SecCurve::{$fn}->getOrder() test failed: got " . $obj->getOrder() . ", expected " . $result[0] . ".<br />";
            }

            if ($obj->getCurve()->getPrime() != $result[1]) {
                self::$errors++;
                print "*** SecCurve::{$fn}->getCurve()->getPrime() test failed: got " . $obj->getCurve()->getPrime() . ", expected " . $result[1] . ".<br />";
            }
        }
    }
}
