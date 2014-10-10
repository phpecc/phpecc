<?php

namespace Mdanter\Ecc;

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
 * This class is the implementation of ECDH.
 * EcDH is safe key exchange and achieves
 * that a key is transported securely between two parties.
 * The key then can be hashed and used as a basis in
 * a dual encryption scheme, along with AES for faster
 * two- way encryption.
 *
 * @author Matej Danter
 */
class EcDH implements EcDHInterface
{
    /**
     * Adapter used for math calculatioins
     *
     * @var MathAdapter
     */
    private $adapter;

    /**
     * Private key generator point
     *
     * @var PointInterface
     */
    private $generator;

    /**
     * Public key point derived from generator point
     *
     * @var PointInterface
     */
    private $pubPoint = null;

    /**
     * Public key point of other party.
     *
     * @var PointInterface
     */
    private $receivedPubPoint = null;

    /**
     * Secret used to derive the public key point.
     *
     * @var int|string
     */
    private $secret = 0;

    /**
     * Shared key between the two parties
     *
     * @var int|string
     */
    private $sharedSecretKey = null;

    /**
     * Initialize a new exchange from a generator point.
     *
     * @param GeneratorPoint $g Generator used to create the private key secret.
     * @param MathAdapter $adapter A math adapter instance.
     */
    public function __construct(GeneratorPoint $g, MathAdapter $adapter)
    {
        $this->generator = $g;
        $this->adapter = $adapter;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\EcDHInterface::calculateKey()
     */
    public function calculateKey()
    {
        $this->checkExchangeState();

        $this->sharedSecretKey = $this->receivedPubPoint->mul($this->secret)->getX();
    }

    /**
     * Performs a key exchange with another party and calculates the shared secret for the exchange.
     *
     * @param EcDHInterface $other
     * @param bool $forceNewKeys
     */
    public function exchangeKeys(EcDHInterface $other, $forceNewKeys = false)
    {
        $this->setPublicPoint($other->getPublicPoint($forceNewKeys));
        $other->setPublicPoint($this->getPublicPoint($forceNewKeys));

        $this->calculateKey();
        $other->calculateKey();
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\EcDHInterface::getSharedKey()
     */
    public function getSharedKey()
    {
        $this->checkEncryptionState();

        return $this->sharedSecretKey;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\EcDHInterface::getPublicPoint()
     */
    public function getPublicPoint($regenerate = false)
    {
        if ($this->pubPoint === null || $regenerate) {
            $this->pubPoint = $this->calculatePublicPoint();
        }

        return $this->pubPoint;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\EcDHInterface::setPublicPoint()
     */
    public function setPublicPoint(PointInterface $q)
    {
        $this->receivedPubPoint = $q;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\EcDHInterface::encrypt()
     */
    public function encrypt($string)
    {
        $key = hash("sha256", $this->sharedSecretKey, true);

        $cypherText = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, base64_encode($string), MCRYPT_MODE_CBC, $key);

        return $cypherText;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\EcDHInterface::decrypt()
     */
    public function decrypt($string)
    {
        $key = hash("sha256", $this->sharedSecretKey, true);

        $clearText = base64_decode(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_CBC, $key));

        return $clearText;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\EcDHInterface::encryptFile()
     */
    public function encryptFile($path)
    {
        if (file_exists($path) && is_readable($path)) {
            return $this->encrypt(file_get_contents($path));
        }

        throw new \InvalidArgumentException("File '$path' does not exist or is not readable.");
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\EcDHInterface::decryptFile()
     */
    public function decryptFile($path)
    {
        if (file_exists($path) && is_readable($path)) {
            return $this->decrypt(file_get_contents($path));
        }

        throw new \InvalidArgumentException("File '$path' does not exist or is not readable.");
    }

    /**
     * Calculates a new public point for the exchange.
     */
    private function calculatePublicPoint()
    {
        if ($this->secret == 0) {
            $this->secret = $this->calculateSecret();
        }

        // Alice computes da * generator Qa is public, da is private
        return $this->generator->mul($this->secret);
    }

    /**
     * Calculates a random value to be used as the private key secret.
     *
     * @return int|string
     */
    private function calculateSecret()
    {
        // Alice selects a random number between 1 and the order of the generator point(private)
        $n = $this->generator->getOrder();
        $r = $this->adapter->rand($n);

        while ($r == 0) {
            $r = $this->adapter->rand($n);
        }

        return $r;
    }

    /**
     * Verifies that the shared secret key is available.
     *
     * @throws \RuntimeException when the key is not available.
     */
    private function checkEncryptionState()
    {
        if ($this->sharedSecretKey === null) {
            throw new \RuntimeException('Shared secret is not set, a public key exchange is required first.');
        }
    }

    /**
     * Verifies that a public key exchange has been made.
     * @throws \RuntimeException when the exchange has not been made.
     */
    private function checkExchangeState()
    {
        if ($this->receivedPubPoint === null) {
            throw new \RuntimeException('Recipient key not set, a public key exchange is required first.');
        }
    }
}
