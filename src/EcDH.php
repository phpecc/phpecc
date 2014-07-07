<?php
/***********************************************************************
Copyright (C) 2012 Matyas Danter

Permission is hereby granted, free of charge, to any person obtaining 
a copy of this software and associated documentation files (the "Software"), 
to deal in the Software without restriction, including without limitation 
the rights to use, copy, modify, merge, publish, distribute, sublicense, 
and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included 
in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES 
OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, 
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR 
OTHER DEALINGS IN THE SOFTWARE.
*************************************************************************/

/**
 * This class is the implementation of ECDH.
 * EcDH is safe key exchange and achieves
 * that a key is transported securely between two parties.
 * The key then can be hashed and used as a basis in
 * a dual encryption scheme, along with AES for faster
 * two- way encryption.
 *
 * @author Matej Danter
 */
class EcDH implements EcDHInterface {

    private $generator;
    private $pubPoint;
    private $receivedPubPoint;
    private $secret;
    private $agreed_key;

    public function __construct(Point $g) {
        $this->generator = $g;
    }

    public function calculateKey() {

        return $this->agreed_key = Point::mul($this->secret, $this->receivedPubPoint)->getX();
    }

    public function getPublicPoint() {
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            //alice selects a random number between 1 and the order of the generator point(private)
            $n = $this->generator->getOrder();

            $this->secret = gmp_Utils::gmp_random($n);

            //Alice computes da * generator Qa is public, da is private
            $this->pubPoint = Point::mul($this->secret, $this->generator);

            return $this->pubPoint;
        } else if (extension_loaded('bcmath') && USE_EXT == 'BCMATH') {
            //alice selects a random number between 1 and the order of the generator point(private)
            $n = $this->generator->getOrder();

            $this->secret = bcmath_Utils::bcrand($n);

            //Alice computes da * generator Qa is public, da is private
            $this->pubPoint = Point::mul($this->secret, $this->generator);

            return $this->pubPoint;
        } else {
            throw new ErrorException("Please Install BCMATH or GMP.");
        }
    }

    public function setPublicPoint(Point $q) {
        $this->receivedPubPoint = $q;
    }

    public function encrypt($string) {
        $key = hash("sha256", $this->agreed_key, true);

        $cypherText = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, base64_encode($string), MCRYPT_MODE_CBC, $key);

        return $cypherText;
    }

    public function decrypt($string) {
        $key = hash("sha256", $this->agreed_key, true);

        $clearText = base64_decode(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_CBC, $key));
        return $clearText;
    }

    public function encryptFile($path) {

        if (file_exists($path)) {
            $string = file_get_contents($path);

            $key = hash("sha256", $this->agreed_key, true);

            $cypherText = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, base64_encode($string), MCRYPT_MODE_CBC, $key);

            return $cypherText;
        }
    }

    public function decryptFile($path) {

        if (file_exists($path)) {
            $string = file_get_contents($path);

            $key = hash("sha256", $this->agreed_key, true);

            $clearText = base64_decode(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_CBC, $key));

            return $clearText;
        }
    }

}

?>
