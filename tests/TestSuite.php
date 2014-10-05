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
            self::test_SECGcurve();
        }
        elseif (extension_loaded('bcmath')) {
            self::test_SECGcurve();
        }
        else {
            echo "Please install GMP or BCMATH. For higher performance GMP is preferred.";
            self::$errors++;
        }

        return self::$errors;
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
