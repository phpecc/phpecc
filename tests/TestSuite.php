<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\NumberTheory;
use Mdanter\Ecc\CurveFp;
use Mdanter\Ecc\Point;
use Mdanter\Ecc\NISTcurve;
use Mdanter\Ecc\GmpUtils;
use Mdanter\Ecc\PublicKey;
use Mdanter\Ecc\PrivateKey;
use Mdanter\Ecc\Signature;
use Mdanter\Ecc\EcDH;
use Mdanter\Ecc\BcMathUtils;

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
        
            self::gmp_EcArithmetic($verbose);
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
        
            self::bcmath_EcArithmetic($verbose);
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
            $end_time = microtime(true);
        
            $time_res = $end_time - $start_time;
        
            echo "<br /><h3>TEST SUITE TOTAL TIME : " . $time_res . " seconds. </h3><br />";
        } else {
            echo "Please install GMP or BCMATH. For higher performance GMP is preferred.";
            self::$errors++;
        }
        
        return self::$errors;
    }



    public static function gmp_EcArithmetic($verbose = false)
    {
        $start_time = microtime(true);
        $c = new CurveFp(23, 1, 1);
        if ($verbose)
            echo ">>>>>>>>>>>>>>>>TESTING EC ADD<<<<<<<<<<<<<<<<<<<<<<br />\n";
        self::test_add($c, 3, 10, 9, 7, 17, 20, $verbose);
        if ($verbose)
            echo ">>>>>>>>>>>>>>>>TESTING EC DOUBLE<<<<<<<<<<<<<<<<<<<<<<br />\n";

        self::test_double($c, 3, 10, 7, 12, $verbose);
        if ($verbose)
            echo ">>>>>>>>>>>>>>>>TESTING EC ADD(DOUBLE)<<<<<<<<<<<<<<<<<<<<<<br />\n";
        self::test_add($c, 3, 10, 3, 10, 7, 12, $verbose); // (Should just invoke double.)
        if ($verbose)
            echo ">>>>>>>>>>>>>>>>TESTING EC MULT<<<<<<<<<<<<<<<<<<<<<<br />\n";
        self::test_multiply($c, 3, 10, 2, 7, 12, $verbose);

        $g = new Point($c, 13, 7, 7);
        if ($verbose)
            echo ">>>>>>>>>>>>>>>>PERFORMING INFINITY TESTS<<<<<<<<<<<<<<<<<<<<<<br />\n";
        $check = Point::$infinity;
        for ($i = 0; $i < 8; $i ++) {
            $p = Point::mul(($i % 7), $g);

            if ($p == $check) {
                if ($verbose) {
                    print "$g * $i = $p, expected $check . . .";
                    print " Correct.<br />";
                }
            } else {
                self::$errors++;
                print "$g * $i = $p, expected $check . . .";
                print " Wrong.<br />";
            }
            $check = Point::add($check, $g);
        }
        if ($verbose)
            echo ">>>>>>>>>>>>>>>>END PERFORMING INFINITY TESTS<<<<<<<<<<<<<<<<<<<<<<br />\n";
        $end_time = microtime(true);

        $time_res = $end_time - $start_time;

        echo "<br />Elementary EC arithmetic took: " . $time_res . " seconds. <br />\n";
    }

    public static function gmp_NISTCurveTest($verbose = false)
    {
        $start_time = microtime(true);

        $p192 = NISTcurve::generator_192();

        // From X9.62:

        $d = '651056770906015076056810763456358567190100156695615665659';
        $Q = Point::mul($d, $p192);
        if ($Q->getX() != GmpUtils::gmp_hexdec('0x62B12D60690CDCF330BABAB6E69763B471F994DD702D16A5')) {
            self::$errors++;
            print "*** p192 * d came out wrong.<br />\n";
        }
        else {
            if ($verbose)
                print "p192 * d came out right.<br />\n";
        }

        $k = '6140507067065001063065065565667405560006161556565665656654';

        $R = Point::mul($k, $p192);

        $Check = new Point(NISTcurve::curve_192(), GmpUtils::gmp_hexdec('0x885052380FF147B734C330C43D39B2C4A89F29B0F749FEAD'), GmpUtils::gmp_hexdec('0x9CF9FA1CBEFEFB917747A3BB29C072B9289C2547884FD835'));

        if ($R->getX() != GmpUtils::gmp_hexdec('0x885052380FF147B734C330C43D39B2C4A89F29B0F749FEAD') || $R->getY() != GmpUtils::gmp_hexdec('0x9CF9FA1CBEFEFB917747A3BB29C072B9289C2547884FD835')) {
            self::$errors++;
            print "*** k * p192 came out wrong.<br />$R<br />$Check<br />\n";
        }
        else {
            if ($verbose)
                print "k * p192 came out right.<br />\n";
        }

        $u1 = '2563697409189434185194736134579731015366492496392189760599';
        $u2 = '6266643813348617967186477710235785849136406323338782220568';
        $temp = Point::add(Point::mul($u1, $p192), Point::mul($u2, $Q));
        if ($temp->getX() != GmpUtils::gmp_hexdec('0x885052380FF147B734C330C43D39B2C4A89F29B0F749FEAD') || $temp->getY() != GmpUtils::gmp_hexdec('0x9CF9FA1CBEFEFB917747A3BB29C072B9289C2547884FD835')) {
            self::$errors++;
            print "*** u1 * p192 + u2 * Q came out wrong.<br />\n";
        }
        else {
            if ($verbose)
                print "u1 * p192 + u2 * Q came out right.<br />\n";
        }
        $end_time = microtime(true);

        $time_res = $end_time - $start_time;

        echo "<br />NIST curve validity checking (X9.62) took: " . $time_res . " seconds. <br />\n";
    }

    public static function gmp_pointValidity($verbose = false)
    {
        $p192 = NISTcurve::generator_192();
        if ($verbose)
            print "Testing point validity, as per ECDSAVS.pdf B.2.2:<br /><br />\n";
        $start_time = microtime(true);
        self::test_point_validity($p192, GmpUtils::gmp_hexdec('cd6d0f029a023e9aaca429615b8f577abee685d8257cc83a'), GmpUtils::gmp_hexdec('0x00019c410987680e9fb6c0b6ecc01d9a2647c8bae27721bacdfc'), false, $verbose);

        self::test_point_validity($p192, GmpUtils::gmp_hexdec('00017f2fce203639e9eaf9fb50b81fc32776b30e3b02af16c73b'), GmpUtils::gmp_hexdec('0x95da95c5e72dd48e229d4748d4eee658a9a54111b23b2adb'), false, $verbose);

        self::test_point_validity($p192, GmpUtils::gmp_hexdec('0x4f77f8bc7fccbadd5760f4938746d5f253ee2168c1cf2792'), GmpUtils::gmp_hexdec('0x000147156ff824d131629739817edb197717c41aab5c2a70f0f6'), false, $verbose);

        self::test_point_validity($p192, GmpUtils::gmp_hexdec('0xc58d61f88d905293bcd4cd0080bcb1b7f811f2ffa41979f6'), GmpUtils::gmp_hexdec('0x8804dc7a7c4c7f8b5d437f5156f3312ca7d6de8a0e11867f'), true, $verbose);

        self::test_point_validity($p192, GmpUtils::gmp_hexdec('0xcdf56c1aa3d8afc53c521adf3ffb96734a6a630a4a5b5a70'), GmpUtils::gmp_hexdec('0x97c1c44a5fb229007b5ec5d25f7413d170068ffd023caa4e'), true, $verbose);

        self::test_point_validity($p192, GmpUtils::gmp_hexdec('0x89009c0dc361c81e99280c8e91df578df88cdf4b0cdedced'), GmpUtils::gmp_hexdec('0x27be44a529b7513e727251f128b34262a0fd4d8ec82377b9'), true, $verbose);

        self::test_point_validity($p192, GmpUtils::gmp_hexdec('0x6a223d00bd22c52833409a163e057e5b5da1def2a197dd15'), GmpUtils::gmp_hexdec('0x7b482604199367f1f303f9ef627f922f97023e90eae08abf'), true, $verbose);

        self::test_point_validity($p192, GmpUtils::gmp_hexdec('0x6dccbde75c0948c98dab32ea0bc59fe125cf0fb1a3798eda'), GmpUtils::gmp_hexdec('0x0001171a3e0fa60cf3096f4e116b556198de430e1fbd330c8835'), false, $verbose);

        self::test_point_validity($p192, GmpUtils::gmp_hexdec('0xd266b39e1f491fc4acbbbc7d098430931cfa66d55015af12'), GmpUtils::gmp_hexdec('0x193782eb909e391a3148b7764e6b234aa94e48d30a16dbb2'), false, $verbose);

        self::test_point_validity($p192, GmpUtils::gmp_hexdec('0x9d6ddbcd439baa0c6b80a654091680e462a7d1d3f1ffeb43'), GmpUtils::gmp_hexdec('0x6ad8efc4d133ccf167c44eb4691c80abffb9f82b932b8caa'), false, $verbose);

        self::test_point_validity($p192, GmpUtils::gmp_hexdec('0x146479d944e6bda87e5b35818aa666a4c998a71f4e95edbc'), GmpUtils::gmp_hexdec('0xa86d6fe62bc8fbd88139693f842635f687f132255858e7f6'), false, $verbose);

        self::test_point_validity($p192, GmpUtils::gmp_hexdec('0xe594d4a598046f3598243f50fd2c7bd7d380edb055802253'), GmpUtils::gmp_hexdec('0x509014c0c4d6b536e3ca750ec09066af39b4c8616a53a923'), false, $verbose);

        $end_time = microtime(true);

        $time_res = $end_time - $start_time;

        echo "<br />Point validity testing (ECDSAVS.pdf B.2.2) took: " . $time_res . " seconds. <br />";
    }

    public static function gmp_signatureValidity($verbose = false)
    {
        $start_time = microtime(true);
        $p192 = NISTcurve::generator_192();
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
        }
        else {
            if ($verbose)
                print "r and s came out right.<br />";
        }

        $valid = $pubk->verifies($e, $sig);
        if ($valid) {
            if ($verbose)
                print "Signature verified OK.<br />";
        } else {
            self::$errors++;
            print "*** Signature failed verification.<br />";
        }
        $valid = $pubk->verifies(gmp_strval(gmp_sub($e, 1)), $sig);
        if (! $valid) {
            if ($verbose)
                print "Forgery was correctly rejected.<br />";
        } else {
            self::$errors++;
            print "*** Forgery was erroneously accepted.<br />";
        }

        if ($verbose)
            print "Trying signature-verification tests from ECDSAVS.pdf B.2.4:<br />";
        if ($verbose)
            print "P-192:";

        $Msg = GmpUtils::gmp_hexdec('0x84ce72aa8699df436059f052ac51b6398d2511e49631bcb7e71f89c499b9ee425dfbc13a5f6d408471b054f2655617cbbaf7937b7c80cd8865cf02c8487d30d2b0fbd8b2c4e102e16d828374bbc47b93852f212d5043c3ea720f086178ff798cc4f63f787b9c2e419efa033e7644ea7936f54462dc21a6c4580725f7f0e7d158');
        $Qx = GmpUtils::gmp_hexdec('0xd9dbfb332aa8e5ff091e8ce535857c37c73f6250ffb2e7ac');
        $Qy = GmpUtils::gmp_hexdec('0x282102e364feded3ad15ddf968f88d8321aa268dd483ebc4');
        $R = GmpUtils::gmp_hexdec('0x64dca58a20787c488d11d6dd96313f1b766f2d8efe122916');
        $S = GmpUtils::gmp_hexdec('0x1ecba28141e84ab4ecad92f56720e2cc83eb3d22dec72479');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, true, $verbose);

        $Msg = GmpUtils::gmp_hexdec('0x94bb5bacd5f8ea765810024db87f4224ad71362a3c28284b2b9f39fab86db12e8beb94aae899768229be8fdb6c4f12f28912bb604703a79ccff769c1607f5a91450f30ba0460d359d9126cbd6296be6d9c4bb96c0ee74cbb44197c207f6db326ab6f5a659113a9034e54be7b041ced9dcf6458d7fb9cbfb2744d999f7dfd63f4');
        $Qx = GmpUtils::gmp_hexdec('0x3e53ef8d3112af3285c0e74842090712cd324832d4277ae7');
        $Qy = GmpUtils::gmp_hexdec('0xcc75f8952d30aec2cbb719fc6aa9934590b5d0ff5a83adb7');
        $R = GmpUtils::gmp_hexdec('0x8285261607283ba18f335026130bab31840dcfd9c3e555af');
        $S = GmpUtils::gmp_hexdec('0x356d89e1b04541afc9704a45e9c535ce4a50929e33d7e06c');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, true, $verbose);

        $Msg = GmpUtils::gmp_hexdec('0xf6227a8eeb34afed1621dcc89a91d72ea212cb2f476839d9b4243c66877911b37b4ad6f4448792a7bbba76c63bdd63414b6facab7dc71c3396a73bd7ee14cdd41a659c61c99b779cecf07bc51ab391aa3252386242b9853ea7da67fd768d303f1b9b513d401565b6f1eb722dfdb96b519fe4f9bd5de67ae131e64b40e78c42dd');
        $Qx = GmpUtils::gmp_hexdec('0x16335dbe95f8e8254a4e04575d736befb258b8657f773cb7');
        $Qy = GmpUtils::gmp_hexdec('0x421b13379c59bc9dce38a1099ca79bbd06d647c7f6242336');
        $R = GmpUtils::gmp_hexdec('0x4141bd5d64ea36c5b0bd21ef28c02da216ed9d04522b1e91');
        $S = GmpUtils::gmp_hexdec('0x159a6aa852bcc579e821b7bb0994c0861fb08280c38daa09');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = GmpUtils::gmp_hexdec('0x16b5f93afd0d02246f662761ed8e0dd9504681ed02a253006eb36736b563097ba39f81c8e1bce7a16c1339e345efabbc6baa3efb0612948ae51103382a8ee8bc448e3ef71e9f6f7a9676694831d7f5dd0db5446f179bcb737d4a526367a447bfe2c857521c7f40b6d7d7e01a180d92431fb0bbd29c04a0c420a57b3ed26ccd8a');
        $Qx = GmpUtils::gmp_hexdec('0xfd14cdf1607f5efb7b1793037b15bdf4baa6f7c16341ab0b');
        $Qy = GmpUtils::gmp_hexdec('0x83fa0795cc6c4795b9016dac928fd6bac32f3229a96312c4');
        $R = GmpUtils::gmp_hexdec('0x8dfdb832951e0167c5d762a473c0416c5c15bc1195667dc1');
        $S = GmpUtils::gmp_hexdec('0x1720288a2dc13fa1ec78f763f8fe2ff7354a7e6fdde44520');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = GmpUtils::gmp_hexdec('0x08a2024b61b79d260e3bb43ef15659aec89e5b560199bc82cf7c65c77d39192e03b9a895d766655105edd9188242b91fbde4167f7862d4ddd61e5d4ab55196683d4f13ceb90d87aea6e07eb50a874e33086c4a7cb0273a8e1c4408f4b846bceae1ebaac1b2b2ea851a9b09de322efe34cebe601653efd6ddc876ce8c2f2072fb');
        $Qx = GmpUtils::gmp_hexdec('0x674f941dc1a1f8b763c9334d726172d527b90ca324db8828');
        $Qy = GmpUtils::gmp_hexdec('0x65adfa32e8b236cb33a3e84cf59bfb9417ae7e8ede57a7ff');
        $R = GmpUtils::gmp_hexdec('0x9508b9fdd7daf0d8126f9e2bc5a35e4c6d800b5b804d7796');
        $S = GmpUtils::gmp_hexdec('0x36f2bf6b21b987c77b53bb801b3435a577e3d493744bfab0');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = GmpUtils::gmp_hexdec('0x1843aba74b0789d4ac6b0b8923848023a644a7b70afa23b1191829bbe4397ce15b629bf21a8838298653ed0c19222b95fa4f7390d1b4c844d96e645537e0aae98afb5c0ac3bd0e4c37f8daaff25556c64e98c319c52687c904c4de7240a1cc55cd9756b7edaef184e6e23b385726e9ffcba8001b8f574987c1a3fedaaa83ca6d');
        $Qx = GmpUtils::gmp_hexdec('0x10ecca1aad7220b56a62008b35170bfd5e35885c4014a19f');
        $Qy = GmpUtils::gmp_hexdec('0x04eb61984c6c12ade3bc47f3c629ece7aa0a033b9948d686');
        $R = GmpUtils::gmp_hexdec('0x82bfa4e82c0dfe9274169b86694e76ce993fd83b5c60f325');
        $S = GmpUtils::gmp_hexdec('0xa97685676c59a65dbde002fe9d613431fb183e8006d05633');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = GmpUtils::gmp_hexdec('0x5a478f4084ddd1a7fea038aa9732a822106385797d02311aeef4d0264f824f698df7a48cfb6b578cf3da416bc0799425bb491be5b5ecc37995b85b03420a98f2c4dc5c31a69a379e9e322fbe706bbcaf0f77175e05cbb4fa162e0da82010a278461e3e974d137bc746d1880d6eb02aa95216014b37480d84b87f717bb13f76e1');
        $Qx = GmpUtils::gmp_hexdec('0x6636653cb5b894ca65c448277b29da3ad101c4c2300f7c04');
        $Qy = GmpUtils::gmp_hexdec('0xfdf1cbb3fc3fd6a4f890b59e554544175fa77dbdbeb656c1');
        $R = GmpUtils::gmp_hexdec('0xeac2ddecddfb79931a9c3d49c08de0645c783a24cb365e1c');
        $S = GmpUtils::gmp_hexdec('0x3549fee3cfa7e5f93bc47d92d8ba100e881a2a93c22f8d50');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = GmpUtils::gmp_hexdec('0xc598774259a058fa65212ac57eaa4f52240e629ef4c310722088292d1d4af6c39b49ce06ba77e4247b20637174d0bd67c9723feb57b5ead232b47ea452d5d7a089f17c00b8b6767e434a5e16c231ba0efa718a340bf41d67ea2d295812ff1b9277daacb8bc27b50ea5e6443bcf95ef4e9f5468fe78485236313d53d1c68f6ba2');
        $Qx = GmpUtils::gmp_hexdec('0xa82bd718d01d354001148cd5f69b9ebf38ff6f21898f8aaa');
        $Qy = GmpUtils::gmp_hexdec('0xe67ceede07fc2ebfafd62462a51e4b6c6b3d5b537b7caf3e');
        $R = GmpUtils::gmp_hexdec('0x4d292486c620c3de20856e57d3bb72fcde4a73ad26376955');
        $S = GmpUtils::gmp_hexdec('0xa85289591a6081d5728825520e62ff1c64f94235c04c7f95');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = GmpUtils::gmp_hexdec('0xca98ed9db081a07b7557f24ced6c7b9891269a95d2026747add9e9eb80638a961cf9c71a1b9f2c29744180bd4c3d3db60f2243c5c0b7cc8a8d40a3f9a7fc910250f2187136ee6413ffc67f1a25e1c4c204fa9635312252ac0e0481d89b6d53808f0c496ba87631803f6c572c1f61fa049737fdacce4adff757afed4f05beb658');
        $Qx = GmpUtils::gmp_hexdec('0x7d3b016b57758b160c4fca73d48df07ae3b6b30225126c2f');
        $Qy = GmpUtils::gmp_hexdec('0x4af3790d9775742bde46f8da876711be1b65244b2b39e7ec');
        $R = GmpUtils::gmp_hexdec('0x95f778f5f656511a5ab49a5d69ddd0929563c29cbc3a9e62');
        $S = GmpUtils::gmp_hexdec('0x75c87fc358c251b4c83d2dd979faad496b539f9f2ee7a289');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = GmpUtils::gmp_hexdec('0x31dd9a54c8338bea06b87eca813d555ad1850fac9742ef0bbe40dad400e10288acc9c11ea7dac79eb16378ebea9490e09536099f1b993e2653cd50240014c90a9c987f64545abc6a536b9bd2435eb5e911fdfde2f13be96ea36ad38df4ae9ea387b29cced599af777338af2794820c9cce43b51d2112380a35802ab7e396c97a');
        $Qx = GmpUtils::gmp_hexdec('0x9362f28c4ef96453d8a2f849f21e881cd7566887da8beb4a');
        $Qy = GmpUtils::gmp_hexdec('0xe64d26d8d74c48a024ae85d982ee74cd16046f4ee5333905');
        $R = GmpUtils::gmp_hexdec('0xf3923476a296c88287e8de914b0b324ad5a963319a4fe73b');
        $S = GmpUtils::gmp_hexdec('0xf0baeed7624ed00d15244d8ba2aede085517dbdec8ac65f5');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, true, $verbose);

        $Msg = GmpUtils::gmp_hexdec('0xb2b94e4432267c92f9fdb9dc6040c95ffa477652761290d3c7de312283f6450d89cc4aabe748554dfb6056b2d8e99c7aeaad9cdddebdee9dbc099839562d9064e68e7bb5f3a6bba0749ca9a538181fc785553a4000785d73cc207922f63e8ce1112768cb1de7b673aed83a1e4a74592f1268d8e2a4e9e63d414b5d442bd0456d');
        $Qx = GmpUtils::gmp_hexdec('0xcc6fc032a846aaac25533eb033522824f94e670fa997ecef');
        $Qy = GmpUtils::gmp_hexdec('0xe25463ef77a029eccda8b294fd63dd694e38d223d30862f1');
        $R = GmpUtils::gmp_hexdec('0x066b1d07f3a40e679b620eda7f550842a35c18b80c5ebe06');
        $S = GmpUtils::gmp_hexdec('0xa0b0fb201e8f2df65e2c4508ef303bdc90d934016f16b2dc');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = GmpUtils::gmp_hexdec('0x4366fcadf10d30d086911de30143da6f579527036937007b337f7282460eae5678b15cccda853193ea5fc4bc0a6b9d7a31128f27e1214988592827520b214eed5052f7775b750b0c6b15f145453ba3fee24a085d65287e10509eb5d5f602c440341376b95c24e5c4727d4b859bfe1483d20538acdd92c7997fa9c614f0f839d7');
        $Qx = GmpUtils::gmp_hexdec('0x955c908fe900a996f7e2089bee2f6376830f76a19135e753');
        $Qy = GmpUtils::gmp_hexdec('0xba0c42a91d3847de4a592a46dc3fdaf45a7cc709b90de520');
        $R = GmpUtils::gmp_hexdec('0x1f58ad77fc04c782815a1405b0925e72095d906cbf52a668');
        $S = GmpUtils::gmp_hexdec('0xf2e93758b3af75edf784f05a6761c9b9a6043c66b845b599');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = GmpUtils::gmp_hexdec('0x543f8af57d750e33aa8565e0cae92bfa7a1ff78833093421c2942cadf9986670a5ff3244c02a8225e790fbf30ea84c74720abf99cfd10d02d34377c3d3b41269bea763384f372bb786b5846f58932defa68023136cd571863b304886e95e52e7877f445b9364b3f06f3c28da12707673fecb4b8071de06b6e0a3c87da160cef3');
        $Qx = GmpUtils::gmp_hexdec('0x31f7fa05576d78a949b24812d4383107a9a45bb5fccdd835');
        $Qy = GmpUtils::gmp_hexdec('0x8dc0eb65994a90f02b5e19bd18b32d61150746c09107e76b');
        $R = GmpUtils::gmp_hexdec('0xbe26d59e4e883dde7c286614a767b31e49ad88789d3a78ff');
        $S = GmpUtils::gmp_hexdec('0x8762ca831c1ce42df77893c9b03119428e7a9b819b619068');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false);

        $Msg = GmpUtils::gmp_hexdec('0xd2e8454143ce281e609a9d748014dcebb9d0bc53adb02443a6aac2ffe6cb009f387c346ecb051791404f79e902ee333ad65e5c8cb38dc0d1d39a8dc90add5023572720e5b94b190d43dd0d7873397504c0c7aef2727e628eb6a74411f2e400c65670716cb4a815dc91cbbfeb7cfe8c929e93184c938af2c078584da045e8f8d1');
        $Qx = GmpUtils::gmp_hexdec('0x66aa8edbbdb5cf8e28ceb51b5bda891cae2df84819fe25c0');
        $Qy = GmpUtils::gmp_hexdec('0x0c6bc2f69030a7ce58d4a00e3b3349844784a13b8936f8da');
        $R = GmpUtils::gmp_hexdec('0xa4661e69b1734f4a71b788410a464b71e7ffe42334484f23');
        $S = GmpUtils::gmp_hexdec('0x738421cf5e049159d69c57a915143e226cac8355e149afe9');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = GmpUtils::gmp_hexdec('0x6660717144040f3e2f95a4e25b08a7079c702a8b29babad5a19a87654bc5c5afa261512a11b998a4fb36b5d8fe8bd942792ff0324b108120de86d63f65855e5461184fc96a0a8ffd2ce6d5dfb0230cbbdd98f8543e361b3205f5da3d500fdc8bac6db377d75ebef3cb8f4d1ff738071ad0938917889250b41dd1d98896ca06fb');
        $Qx = GmpUtils::gmp_hexdec('0xbcfacf45139b6f5f690a4c35a5fffa498794136a2353fc77');
        $Qy = GmpUtils::gmp_hexdec('0x6f4a6c906316a6afc6d98fe1f0399d056f128fe0270b0f22');
        $R = GmpUtils::gmp_hexdec('0x9db679a3dafe48f7ccad122933acfe9da0970b71c94c21c1');
        $S = GmpUtils::gmp_hexdec('0x984c2db99827576c0a41a5da41e07d8cc768bc82f18c9da9');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        if ($verbose)
            print "Testing the example code:<br />";

            // Building a public/private key pair from the NIST Curve P-192:

        $g = NISTcurve::generator_192();
        $n = $g->getOrder();

        $secret = GmpUtils::gmp_random($n);

        $secretG = Point::mul($secret, $g);

        $pubkey = new PublicKey($g, Point::mul($secret, $g));

        $privkey = new PrivateKey($pubkey, $secret);

        // Signing a hash value:

        $hash = GmpUtils::gmp_random($n);

        $signature = $privkey->sign($hash, GmpUtils::gmp_random($n));

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
        $g = NISTcurve::generator_192();
        $alice = new EcDH($g);

        $bob = new EcDH($g);

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

        $next_prime = NumberTheory::next_prime($prime);

        $error_tally = 0;

        $cur_prime = $next_prime;

        for ($i = 0; $i < $num_primes; $i ++) {

            $cur_prime = NumberTheory::next_prime($cur_prime);

            if (NumberTheory::is_prime($cur_prime)) {
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

            $calculated = NumberTheory::square_root_mod_prime($sq, $prime);

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
                    $inv = NumberTheory::inverse_mod($a, $m);

                    if ($inv <= 0 || $inv >= $m || ($a * $inv) % $m != 1) {
                        $error_tally ++;
                        self::$errors++;
                        print "$inv = inverse_mod( $a, $m ) is wrong.<br />\n";
                        flush();
                    } else {
                        if ($verbose)
                            print "$inv = inverse_mod( $a, $m ) is CORRECT.<br />\n";
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

    public static function bcmath_EcArithmetic($verbose = false)
    {
        $start_time = microtime(true);
        $c = new CurveFp(23, 1, 1);
        if ($verbose)
            echo ">>>>>>>>>>>>>>>>TESTING EC ADD<<<<<<<<<<<<<<<<<<<<<<br />\n";
        flush();
        self::test_add($c, 3, 10, 9, 7, 17, 20, $verbose);
        if ($verbose)
            echo ">>>>>>>>>>>>>>>>TESTING EC DOUBLE<<<<<<<<<<<<<<<<<<<<<<br />\n";
        flush();
        self::test_double($c, 3, 10, 7, 12, $verbose);
        if ($verbose)
            echo ">>>>>>>>>>>>>>>>TESTING EC ADD(DOUBLE)<<<<<<<<<<<<<<<<<<<<<<br />\n";
        flush();
        self::test_add($c, 3, 10, 3, 10, 7, 12, $verbose); // (Should just invoke double.)
        if ($verbose)
            echo ">>>>>>>>>>>>>>>>TESTING EC MULT<<<<<<<<<<<<<<<<<<<<<<br />\n";
        flush();
        self::test_multiply($c, 3, 10, 2, 7, 12, $verbose);

        $g = new Point($c, 13, 7, 7);
        if ($verbose)
            echo ">>>>>>>>>>>>>>>>PERFORMING INFINITY TESTS<<<<<<<<<<<<<<<<<<<<<<br />\n";
        flush();
        $check = Point::$infinity;
        for ($i = 0; $i < 8; $i ++) {
            $p = Point::mul(($i % 7), $g);

            if ($p == $check) {
                if ($verbose) {
                    echo "$g * $i = $p, expected $check . . .";
                    echo " Correct.<br />";
                    flush();
                }
            } else {
                self::$errors++;
                echo "$g * $i = $p, expected $check . . .";
                echo " Wrong.<br />";
                flush();
            }
            $check = Point::add($check, $g);
        }
        if ($verbose)
            echo ">>>>>>>>>>>>>>>>END PERFORMING INFINITY TESTS<<<<<<<<<<<<<<<<<<<<<<br />\n";
        flush();
        $end_time = microtime(true);

        $time_res = $end_time - $start_time;

        echo "<br />Elementary EC arithmetic took: " . $time_res . " seconds. <br />\n";
        flush();
    }

    public static function bcmath_NISTCurveTest($verbose = false)
    {
        $start_time = microtime(true);

        $p192 = NISTcurve::generator_192();

        // From X9.62:

        $d = '651056770906015076056810763456358567190100156695615665659';
        $Q = Point::mul($d, $p192);
        if ($Q->getX() != BcMathUtils::bchexdec('0x62B12D60690CDCF330BABAB6E69763B471F994DD702D16A5')) {
            self::$errors++;
            echo "*** p192 * d came out wrong.<br />\n";
            flush();
        } else {
            if ($verbose)
                echo "p192 * d came out right.<br />\n";
            flush();
        }

        $k = '6140507067065001063065065565667405560006161556565665656654';

        $R = Point::mul($k, $p192);

        $Check = new Point(NISTcurve::curve_192(), BcMathUtils::bchexdec('0x885052380FF147B734C330C43D39B2C4A89F29B0F749FEAD'), BcMathUtils::bchexdec('0x9CF9FA1CBEFEFB917747A3BB29C072B9289C2547884FD835'));

        if ($R->getX() != BcMathUtils::bchexdec('0x885052380FF147B734C330C43D39B2C4A89F29B0F749FEAD') || $R->getY() != BcMathUtils::bchexdec('0x9CF9FA1CBEFEFB917747A3BB29C072B9289C2547884FD835')) {
            self::$errors++;
            print "*** k * p192 came out wrong.<br />$R<br />$Check<br />\n";
            flush();
        } else {
            if ($verbose)
                print "k * p192 came out right.<br />\n";
            flush();
        }

        $u1 = '2563697409189434185194736134579731015366492496392189760599';
        $u2 = '6266643813348617967186477710235785849136406323338782220568';
        $temp = Point::add(Point::mul($u1, $p192), Point::mul($u2, $Q));
        if ($temp->getX() != BcMathUtils::bchexdec('0x885052380FF147B734C330C43D39B2C4A89F29B0F749FEAD') || $temp->getY() != BcMathUtils::bchexdec('0x9CF9FA1CBEFEFB917747A3BB29C072B9289C2547884FD835')) {
            self::$errors++;
            print "*** u1 * p192 + u2 * Q came out wrong.<br />\n";
            flush();
        } else {
            if ($verbose)
                print "u1 * p192 + u2 * Q came out right.<br />\n";
            flush();
        }
        $end_time = microtime(true);

        $time_res = $end_time - $start_time;

        echo "<br />NIST curve validity checking (X9.62) took: " . $time_res . " seconds. <br />\n";
        flush();
    }

    public static function bcmath_pointValidity($verbose = false)
    {
        $p192 = NISTcurve::generator_192();
        if ($verbose)
            print "Testing point validity, as per ECDSAVS.pdf B.2.2:<br /><br />\n";
        flush();
        $start_time = microtime(true);
        self::test_point_validity($p192, BcMathUtils::bchexdec('cd6d0f029a023e9aaca429615b8f577abee685d8257cc83a'), BcMathUtils::bchexdec('0x00019c410987680e9fb6c0b6ecc01d9a2647c8bae27721bacdfc'), false, $verbose);

        self::test_point_validity($p192, BcMathUtils::bchexdec('00017f2fce203639e9eaf9fb50b81fc32776b30e3b02af16c73b'), BcMathUtils::bchexdec('0x95da95c5e72dd48e229d4748d4eee658a9a54111b23b2adb'), false, $verbose);

        self::test_point_validity($p192, BcMathUtils::bchexdec('0x4f77f8bc7fccbadd5760f4938746d5f253ee2168c1cf2792'), BcMathUtils::bchexdec('0x000147156ff824d131629739817edb197717c41aab5c2a70f0f6'), false, $verbose);

        self::test_point_validity($p192, BcMathUtils::bchexdec('0xc58d61f88d905293bcd4cd0080bcb1b7f811f2ffa41979f6'), BcMathUtils::bchexdec('0x8804dc7a7c4c7f8b5d437f5156f3312ca7d6de8a0e11867f'), true, $verbose);

        self::test_point_validity($p192, BcMathUtils::bchexdec('0xcdf56c1aa3d8afc53c521adf3ffb96734a6a630a4a5b5a70'), BcMathUtils::bchexdec('0x97c1c44a5fb229007b5ec5d25f7413d170068ffd023caa4e'), true, $verbose);

        self::test_point_validity($p192, BcMathUtils::bchexdec('0x89009c0dc361c81e99280c8e91df578df88cdf4b0cdedced'), BcMathUtils::bchexdec('0x27be44a529b7513e727251f128b34262a0fd4d8ec82377b9'), true, $verbose);

        self::test_point_validity($p192, BcMathUtils::bchexdec('0x6a223d00bd22c52833409a163e057e5b5da1def2a197dd15'), BcMathUtils::bchexdec('0x7b482604199367f1f303f9ef627f922f97023e90eae08abf'), true, $verbose);

        self::test_point_validity($p192, BcMathUtils::bchexdec('0x6dccbde75c0948c98dab32ea0bc59fe125cf0fb1a3798eda'), BcMathUtils::bchexdec('0x0001171a3e0fa60cf3096f4e116b556198de430e1fbd330c8835'), false, $verbose);

        self::test_point_validity($p192, BcMathUtils::bchexdec('0xd266b39e1f491fc4acbbbc7d098430931cfa66d55015af12'), BcMathUtils::bchexdec('0x193782eb909e391a3148b7764e6b234aa94e48d30a16dbb2'), false, $verbose);

        self::test_point_validity($p192, BcMathUtils::bchexdec('0x9d6ddbcd439baa0c6b80a654091680e462a7d1d3f1ffeb43'), BcMathUtils::bchexdec('0x6ad8efc4d133ccf167c44eb4691c80abffb9f82b932b8caa'), false, $verbose);

        self::test_point_validity($p192, BcMathUtils::bchexdec('0x146479d944e6bda87e5b35818aa666a4c998a71f4e95edbc'), BcMathUtils::bchexdec('0xa86d6fe62bc8fbd88139693f842635f687f132255858e7f6'), false, $verbose);

        self::test_point_validity($p192, BcMathUtils::bchexdec('0xe594d4a598046f3598243f50fd2c7bd7d380edb055802253'), BcMathUtils::bchexdec('0x509014c0c4d6b536e3ca750ec09066af39b4c8616a53a923'), false, $verbose);

        $end_time = microtime(true);

        $time_res = $end_time - $start_time;

        echo "<br />Point validity testing (ECDSAVS.pdf B.2.2) took: " . $time_res . " seconds. <br />";
        flush();
    }

    public static function bcmath_signatureValidity($verbose = false)
    {
        $start_time = microtime(true);
        $p192 = NISTcurve::generator_192();
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
            print "Trying signature-verification tests from ECDSAVS.pdf B.2.4:<br />";
        flush();
        if ($verbose)
            print "P-192:";
        flush();

        $Msg = BcMathUtils::bchexdec('0x84ce72aa8699df436059f052ac51b6398d2511e49631bcb7e71f89c499b9ee425dfbc13a5f6d408471b054f2655617cbbaf7937b7c80cd8865cf02c8487d30d2b0fbd8b2c4e102e16d828374bbc47b93852f212d5043c3ea720f086178ff798cc4f63f787b9c2e419efa033e7644ea7936f54462dc21a6c4580725f7f0e7d158');
        $Qx = BcMathUtils::bchexdec('0xd9dbfb332aa8e5ff091e8ce535857c37c73f6250ffb2e7ac');
        $Qy = BcMathUtils::bchexdec('0x282102e364feded3ad15ddf968f88d8321aa268dd483ebc4');
        $R = BcMathUtils::bchexdec('0x64dca58a20787c488d11d6dd96313f1b766f2d8efe122916');
        $S = BcMathUtils::bchexdec('0x1ecba28141e84ab4ecad92f56720e2cc83eb3d22dec72479');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, true, $verbose);

        $Msg = BcMathUtils::bchexdec('0x94bb5bacd5f8ea765810024db87f4224ad71362a3c28284b2b9f39fab86db12e8beb94aae899768229be8fdb6c4f12f28912bb604703a79ccff769c1607f5a91450f30ba0460d359d9126cbd6296be6d9c4bb96c0ee74cbb44197c207f6db326ab6f5a659113a9034e54be7b041ced9dcf6458d7fb9cbfb2744d999f7dfd63f4');
        $Qx = BcMathUtils::bchexdec('0x3e53ef8d3112af3285c0e74842090712cd324832d4277ae7');
        $Qy = BcMathUtils::bchexdec('0xcc75f8952d30aec2cbb719fc6aa9934590b5d0ff5a83adb7');
        $R = BcMathUtils::bchexdec('0x8285261607283ba18f335026130bab31840dcfd9c3e555af');
        $S = BcMathUtils::bchexdec('0x356d89e1b04541afc9704a45e9c535ce4a50929e33d7e06c');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, true, $verbose);

        $Msg = BcMathUtils::bchexdec('0xf6227a8eeb34afed1621dcc89a91d72ea212cb2f476839d9b4243c66877911b37b4ad6f4448792a7bbba76c63bdd63414b6facab7dc71c3396a73bd7ee14cdd41a659c61c99b779cecf07bc51ab391aa3252386242b9853ea7da67fd768d303f1b9b513d401565b6f1eb722dfdb96b519fe4f9bd5de67ae131e64b40e78c42dd');
        $Qx = BcMathUtils::bchexdec('0x16335dbe95f8e8254a4e04575d736befb258b8657f773cb7');
        $Qy = BcMathUtils::bchexdec('0x421b13379c59bc9dce38a1099ca79bbd06d647c7f6242336');
        $R = BcMathUtils::bchexdec('0x4141bd5d64ea36c5b0bd21ef28c02da216ed9d04522b1e91');
        $S = BcMathUtils::bchexdec('0x159a6aa852bcc579e821b7bb0994c0861fb08280c38daa09');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = BcMathUtils::bchexdec('0x16b5f93afd0d02246f662761ed8e0dd9504681ed02a253006eb36736b563097ba39f81c8e1bce7a16c1339e345efabbc6baa3efb0612948ae51103382a8ee8bc448e3ef71e9f6f7a9676694831d7f5dd0db5446f179bcb737d4a526367a447bfe2c857521c7f40b6d7d7e01a180d92431fb0bbd29c04a0c420a57b3ed26ccd8a');
        $Qx = BcMathUtils::bchexdec('0xfd14cdf1607f5efb7b1793037b15bdf4baa6f7c16341ab0b');
        $Qy = BcMathUtils::bchexdec('0x83fa0795cc6c4795b9016dac928fd6bac32f3229a96312c4');
        $R = BcMathUtils::bchexdec('0x8dfdb832951e0167c5d762a473c0416c5c15bc1195667dc1');
        $S = BcMathUtils::bchexdec('0x1720288a2dc13fa1ec78f763f8fe2ff7354a7e6fdde44520');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = BcMathUtils::bchexdec('0x08a2024b61b79d260e3bb43ef15659aec89e5b560199bc82cf7c65c77d39192e03b9a895d766655105edd9188242b91fbde4167f7862d4ddd61e5d4ab55196683d4f13ceb90d87aea6e07eb50a874e33086c4a7cb0273a8e1c4408f4b846bceae1ebaac1b2b2ea851a9b09de322efe34cebe601653efd6ddc876ce8c2f2072fb');
        $Qx = BcMathUtils::bchexdec('0x674f941dc1a1f8b763c9334d726172d527b90ca324db8828');
        $Qy = BcMathUtils::bchexdec('0x65adfa32e8b236cb33a3e84cf59bfb9417ae7e8ede57a7ff');
        $R = BcMathUtils::bchexdec('0x9508b9fdd7daf0d8126f9e2bc5a35e4c6d800b5b804d7796');
        $S = BcMathUtils::bchexdec('0x36f2bf6b21b987c77b53bb801b3435a577e3d493744bfab0');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = BcMathUtils::bchexdec('0x1843aba74b0789d4ac6b0b8923848023a644a7b70afa23b1191829bbe4397ce15b629bf21a8838298653ed0c19222b95fa4f7390d1b4c844d96e645537e0aae98afb5c0ac3bd0e4c37f8daaff25556c64e98c319c52687c904c4de7240a1cc55cd9756b7edaef184e6e23b385726e9ffcba8001b8f574987c1a3fedaaa83ca6d');
        $Qx = BcMathUtils::bchexdec('0x10ecca1aad7220b56a62008b35170bfd5e35885c4014a19f');
        $Qy = BcMathUtils::bchexdec('0x04eb61984c6c12ade3bc47f3c629ece7aa0a033b9948d686');
        $R = BcMathUtils::bchexdec('0x82bfa4e82c0dfe9274169b86694e76ce993fd83b5c60f325');
        $S = BcMathUtils::bchexdec('0xa97685676c59a65dbde002fe9d613431fb183e8006d05633');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = BcMathUtils::bchexdec('0x5a478f4084ddd1a7fea038aa9732a822106385797d02311aeef4d0264f824f698df7a48cfb6b578cf3da416bc0799425bb491be5b5ecc37995b85b03420a98f2c4dc5c31a69a379e9e322fbe706bbcaf0f77175e05cbb4fa162e0da82010a278461e3e974d137bc746d1880d6eb02aa95216014b37480d84b87f717bb13f76e1');
        $Qx = BcMathUtils::bchexdec('0x6636653cb5b894ca65c448277b29da3ad101c4c2300f7c04');
        $Qy = BcMathUtils::bchexdec('0xfdf1cbb3fc3fd6a4f890b59e554544175fa77dbdbeb656c1');
        $R = BcMathUtils::bchexdec('0xeac2ddecddfb79931a9c3d49c08de0645c783a24cb365e1c');
        $S = BcMathUtils::bchexdec('0x3549fee3cfa7e5f93bc47d92d8ba100e881a2a93c22f8d50');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = BcMathUtils::bchexdec('0xc598774259a058fa65212ac57eaa4f52240e629ef4c310722088292d1d4af6c39b49ce06ba77e4247b20637174d0bd67c9723feb57b5ead232b47ea452d5d7a089f17c00b8b6767e434a5e16c231ba0efa718a340bf41d67ea2d295812ff1b9277daacb8bc27b50ea5e6443bcf95ef4e9f5468fe78485236313d53d1c68f6ba2');
        $Qx = BcMathUtils::bchexdec('0xa82bd718d01d354001148cd5f69b9ebf38ff6f21898f8aaa');
        $Qy = BcMathUtils::bchexdec('0xe67ceede07fc2ebfafd62462a51e4b6c6b3d5b537b7caf3e');
        $R = BcMathUtils::bchexdec('0x4d292486c620c3de20856e57d3bb72fcde4a73ad26376955');
        $S = BcMathUtils::bchexdec('0xa85289591a6081d5728825520e62ff1c64f94235c04c7f95');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = BcMathUtils::bchexdec('0xca98ed9db081a07b7557f24ced6c7b9891269a95d2026747add9e9eb80638a961cf9c71a1b9f2c29744180bd4c3d3db60f2243c5c0b7cc8a8d40a3f9a7fc910250f2187136ee6413ffc67f1a25e1c4c204fa9635312252ac0e0481d89b6d53808f0c496ba87631803f6c572c1f61fa049737fdacce4adff757afed4f05beb658');
        $Qx = BcMathUtils::bchexdec('0x7d3b016b57758b160c4fca73d48df07ae3b6b30225126c2f');
        $Qy = BcMathUtils::bchexdec('0x4af3790d9775742bde46f8da876711be1b65244b2b39e7ec');
        $R = BcMathUtils::bchexdec('0x95f778f5f656511a5ab49a5d69ddd0929563c29cbc3a9e62');
        $S = BcMathUtils::bchexdec('0x75c87fc358c251b4c83d2dd979faad496b539f9f2ee7a289');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = BcMathUtils::bchexdec('0x31dd9a54c8338bea06b87eca813d555ad1850fac9742ef0bbe40dad400e10288acc9c11ea7dac79eb16378ebea9490e09536099f1b993e2653cd50240014c90a9c987f64545abc6a536b9bd2435eb5e911fdfde2f13be96ea36ad38df4ae9ea387b29cced599af777338af2794820c9cce43b51d2112380a35802ab7e396c97a');
        $Qx = BcMathUtils::bchexdec('0x9362f28c4ef96453d8a2f849f21e881cd7566887da8beb4a');
        $Qy = BcMathUtils::bchexdec('0xe64d26d8d74c48a024ae85d982ee74cd16046f4ee5333905');
        $R = BcMathUtils::bchexdec('0xf3923476a296c88287e8de914b0b324ad5a963319a4fe73b');
        $S = BcMathUtils::bchexdec('0xf0baeed7624ed00d15244d8ba2aede085517dbdec8ac65f5');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, true, $verbose);

        $Msg = BcMathUtils::bchexdec('0xb2b94e4432267c92f9fdb9dc6040c95ffa477652761290d3c7de312283f6450d89cc4aabe748554dfb6056b2d8e99c7aeaad9cdddebdee9dbc099839562d9064e68e7bb5f3a6bba0749ca9a538181fc785553a4000785d73cc207922f63e8ce1112768cb1de7b673aed83a1e4a74592f1268d8e2a4e9e63d414b5d442bd0456d');
        $Qx = BcMathUtils::bchexdec('0xcc6fc032a846aaac25533eb033522824f94e670fa997ecef');
        $Qy = BcMathUtils::bchexdec('0xe25463ef77a029eccda8b294fd63dd694e38d223d30862f1');
        $R = BcMathUtils::bchexdec('0x066b1d07f3a40e679b620eda7f550842a35c18b80c5ebe06');
        $S = BcMathUtils::bchexdec('0xa0b0fb201e8f2df65e2c4508ef303bdc90d934016f16b2dc');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = BcMathUtils::bchexdec('0x4366fcadf10d30d086911de30143da6f579527036937007b337f7282460eae5678b15cccda853193ea5fc4bc0a6b9d7a31128f27e1214988592827520b214eed5052f7775b750b0c6b15f145453ba3fee24a085d65287e10509eb5d5f602c440341376b95c24e5c4727d4b859bfe1483d20538acdd92c7997fa9c614f0f839d7');
        $Qx = BcMathUtils::bchexdec('0x955c908fe900a996f7e2089bee2f6376830f76a19135e753');
        $Qy = BcMathUtils::bchexdec('0xba0c42a91d3847de4a592a46dc3fdaf45a7cc709b90de520');
        $R = BcMathUtils::bchexdec('0x1f58ad77fc04c782815a1405b0925e72095d906cbf52a668');
        $S = BcMathUtils::bchexdec('0xf2e93758b3af75edf784f05a6761c9b9a6043c66b845b599');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = BcMathUtils::bchexdec('0x543f8af57d750e33aa8565e0cae92bfa7a1ff78833093421c2942cadf9986670a5ff3244c02a8225e790fbf30ea84c74720abf99cfd10d02d34377c3d3b41269bea763384f372bb786b5846f58932defa68023136cd571863b304886e95e52e7877f445b9364b3f06f3c28da12707673fecb4b8071de06b6e0a3c87da160cef3');
        $Qx = BcMathUtils::bchexdec('0x31f7fa05576d78a949b24812d4383107a9a45bb5fccdd835');
        $Qy = BcMathUtils::bchexdec('0x8dc0eb65994a90f02b5e19bd18b32d61150746c09107e76b');
        $R = BcMathUtils::bchexdec('0xbe26d59e4e883dde7c286614a767b31e49ad88789d3a78ff');
        $S = BcMathUtils::bchexdec('0x8762ca831c1ce42df77893c9b03119428e7a9b819b619068');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false);

        $Msg = BcMathUtils::bchexdec('0xd2e8454143ce281e609a9d748014dcebb9d0bc53adb02443a6aac2ffe6cb009f387c346ecb051791404f79e902ee333ad65e5c8cb38dc0d1d39a8dc90add5023572720e5b94b190d43dd0d7873397504c0c7aef2727e628eb6a74411f2e400c65670716cb4a815dc91cbbfeb7cfe8c929e93184c938af2c078584da045e8f8d1');
        $Qx = BcMathUtils::bchexdec('0x66aa8edbbdb5cf8e28ceb51b5bda891cae2df84819fe25c0');
        $Qy = BcMathUtils::bchexdec('0x0c6bc2f69030a7ce58d4a00e3b3349844784a13b8936f8da');
        $R = BcMathUtils::bchexdec('0xa4661e69b1734f4a71b788410a464b71e7ffe42334484f23');
        $S = BcMathUtils::bchexdec('0x738421cf5e049159d69c57a915143e226cac8355e149afe9');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        $Msg = BcMathUtils::bchexdec('0x6660717144040f3e2f95a4e25b08a7079c702a8b29babad5a19a87654bc5c5afa261512a11b998a4fb36b5d8fe8bd942792ff0324b108120de86d63f65855e5461184fc96a0a8ffd2ce6d5dfb0230cbbdd98f8543e361b3205f5da3d500fdc8bac6db377d75ebef3cb8f4d1ff738071ad0938917889250b41dd1d98896ca06fb');
        $Qx = BcMathUtils::bchexdec('0xbcfacf45139b6f5f690a4c35a5fffa498794136a2353fc77');
        $Qy = BcMathUtils::bchexdec('0x6f4a6c906316a6afc6d98fe1f0399d056f128fe0270b0f22');
        $R = BcMathUtils::bchexdec('0x9db679a3dafe48f7ccad122933acfe9da0970b71c94c21c1');
        $S = BcMathUtils::bchexdec('0x984c2db99827576c0a41a5da41e07d8cc768bc82f18c9da9');
        self::test_signature_validity($Msg, $Qx, $Qy, $R, $S, false, $verbose);

        if ($verbose)
            print "Testing the example code:<br />";
        flush();
        // Building a public/private key pair from the NIST Curve P-192:

        $g = NISTcurve::generator_192();
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
        $g = NISTcurve::generator_192();
        $alice = new EcDH($g);

        $bob = new EcDH($g);

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

    // generic static methods for curve arithemetic testing
    public static function test_add(CurveFp $c, $x1, $y1, $x2, $y2, $x3, $y3, $verbose = false)
    {
        if (self::$useGmp && extension_loaded('gmp')) {
            // expect that on curve c, (x1, y1) + (x2, y2) = (x3, y3)
            $p1 = new Point($c, $x1, $y1);
            $p2 = new Point($c, $x2, $y2);
            $p3 = Point::add($p1, $p2);
            if ($verbose)
                echo $p1 . " + " . $p2 . " = " . $p3;
            if (GmpUtils::gmp_mod2($p3->getX(), 23) != $x3 || GmpUtils::gmp_mod2($p3->getY(), 23) != $y3) {
                self::$errors++;
                echo " ADD TEST FAILURE: should give: (" . $x3 . " , " . $y3 . ")<br /><br /><br />";
                flush();
            } else {
                if ($verbose)
                    echo " ADD TEST SUCCESSFUL<br /><br /><br />";
                flush();
            }
        } else
            if (extension_loaded('bcmath')) {
                // expect that on curve c, (x1, y1) + (x2, y2) = (x3, y3)
                $p1 = new Point($c, $x1, $y1);
                $p2 = new Point($c, $x2, $y2);
                $p3 = Point::add($p1, $p2);
                if ($verbose)
                    echo $p1 . " + " . $p2 . " = " . $p3;
                if (bcmod($p3->getX(), 23) != $x3 || bcmod($p3->getY(), 23) != $y3) {
                    self::$errors++;
                    echo " ADD TEST FAILURE: should give: (" . $x3 . " , " . $y3 . ")<br /><br /><br />";
                    flush();
                } else {
                    if ($verbose)
                        echo " ADD TEST SUCCESSFUL<br /><br /><br />";
                    flush();
                }
            }
    }

    public static function test_double(CurveFp $c, $x1, $y1, $x3, $y3, $verbose = false)
    {
        if (self::$useGmp && extension_loaded('gmp')) {
            // expect that on curve c, (x1, y1) + (x2, y2) = (x3, y3)
            $p1 = new Point($c, $x1, $y1);
            $p3 = Point::double($p1);
            if ($verbose)
                echo $p1 . " doubled  = " . $p3;
            flush();
            if (GmpUtils::gmp_mod2($p3->getX(), 23) != $x3 || GmpUtils::gmp_mod2($p3->getY(), 23) != $y3) {
                self::$errors++;
                if ($verbose)
                    echo " DOUBLE TEST FAILURE: should give: (" . $x3 . " , " . $y3 . ")<br /><br /><br />";
                flush();
            } else {
                if ($verbose)
                    echo " DOUBLE TEST SUCCESSFUL<br /><br /><br />";
                flush();
            }
        } else
            if (extension_loaded('bcmath')) {
                // expect that on curve c, (x1, y1) + (x2, y2) = (x3, y3)
                $p1 = new Point($c, $x1, $y1);
                $p3 = Point::double($p1);
                if ($verbose)
                    echo $p1 . " doubled  = " . $p3;
                flush();
                if (bcmod($p3->getX(), 23) != $x3 || bcmod($p3->getY(), 23) != $y3) {
                    self::$errors++;
                    if ($verbose)
                        echo " DOUBLE TEST FAILURE: should give: (" . $x3 . " , " . $y3 . ")<br /><br /><br />";
                    flush();
                } else {
                    if ($verbose)
                        echo " DOUBLE TEST SUCCESSFUL<br /><br /><br />";
                    flush();
                }
            }
    }

    public static function test_multiply(CurveFp $c, $x1, $y1, $m, $x3, $y3, $verbose = false)
    {
        if (self::$useGmp && extension_loaded('gmp')) {
            // expect that on curve c, m * (x2, y2) = (x3, y3)
            $p1 = new Point($c, $x1, $y1);
            $p3 = Point::mul($m, $p1);
            if ($verbose)
                echo $p1 . " * " . $m . " = " . $p3;

            if ($p3 instanceof Point) {
                if (GmpUtils::gmp_mod2($p3->getX(), 23) != $x3 || GmpUtils::gmp_mod2($p3->getY(), 23) != $y3) {
                    self::$errors++;
                    echo " MULT TEST FAILURE: should give: (" . $x3 . " , " . $y3 . ")<br /><br /><br />";
                    flush();
                } else {
                    if ($verbose)
                        echo " MULT TEST SUCCESSFUL<br /><br /><br />";
                    flush();
                }
            } else {
                if ($p3 == 'infinity') {
                    self::$errors++;
                    echo " INFINITY MULT TEST FAILURE: should give: (" . $x3 . " , " . $y3 . ")<br /><br /><br />";
                    flush();
                } else {
                    if ($verbose)
                        echo " INFINITY MULT TEST SUCCESSFUL<br /><br /><br />";
                    flush();
                }
            }
        } else
            if (extension_loaded('bcmath')) {
                // expect that on curve c, m * (x2, y2) = (x3, y3)
                $p1 = new Point($c, $x1, $y1);
                $p3 = Point::mul($m, $p1);
                if ($verbose)
                    echo $p1 . " * " . $m . " = " . $p3;
                flush();

                if ($p3 instanceof Point) {
                    if (bcmod($p3->getX(), 23) != $x3 || bcmod($p3->getY(), 23) != $y3) {
                        self::$errors++;
                        echo " MULT TEST FAILURE: should give: (" . $x3 . " , " . $y3 . ")<br /><br /><br />";
                        flush();
                    } else {
                        if ($verbose)
                            echo " MULT TEST SUCCESSFUL<br /><br /><br />";
                        flush();
                    }
                } else {
                    if ($p3 == 'infinity') {
                        self::$errors++;
                        echo " INFINITY MULT TEST FAILURE: should give: (" . $x3 . " , " . $y3 . ")<br /><br /><br />";
                        flush();
                    } else {
                        if ($verbose)
                            echo " INFINITY MULT TEST SUCCESSFUL<br /><br /><br />";
                        flush();
                    }
                }
            }
    }

    public static function test_point_validity($generator, $x, $y, $expected, $verbose = false)
    {
        $res = PrivateKey::point_is_valid($generator, $x, $y);

        if ($res == $expected) {
            if ($verbose)
                print "Point validity tested as expected.<br />";
            flush();
        } else {
            self::$errors++;
            print "Point validity test gave wrong result.<br />";
        }
        flush();
    }

    public static function test_signature_validity($Msg, $Qx, $Qy, $R, $S, $expected, $verbose = false)
    {
        $p192 = NISTcurve::generator_192();
        $curve_192 = NISTcurve::curve_192();

        $pubk = new PublicKey($p192, new Point($curve_192, $Qx, $Qy));
        $got = $pubk->verifies(PrivateKey::digest_integer($Msg), new Signature($R, $S));
        if (bccomp($got, $expected) == 0) {
            if ($verbose)
                print "Signature tested as expected: received " . var_export($got, true) . ", expected " . var_export($expected, true) . ".<br />";
            flush();
        } else {
            self::$errors++;
            print "*** Signature test failed: received " . var_export($got, true) . ", expected " . var_export($expected, true) . ".<br />";
        }
        flush();
    }
}
?>
