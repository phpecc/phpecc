<?php

namespace Mdanter\Ecc\Crypto\EcDH;

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

use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Crypto\Key\PublicKey;
use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Message\EncryptedMessage;
use Mdanter\Ecc\Message\Message;
use Mdanter\Ecc\Message\MessageFactory;
use Mdanter\Ecc\Primitives\PointInterface;
use Mdanter\Ecc\Math\MathAdapterInterface;

/**
 * This class is the implementation of ECDH.
 * EcDH is safe key exchange and achieves
 * that a key is transported securely between two parties.
 * The key then can be hashed and used as a basis in
 * a dual encryption scheme, along with AES for faster
 * two- way encryption.
 */
class EcDH implements EcDHInterface
{
    /**
     * Adapter used for math calculations
     *
     * @var MathAdapterInterface
     */
    private $adapter;

    /**
     * @var \Mdanter\Ecc\Message\MessageFactory
     */
    private $messages;

    /**
     * Secret key between the two parties
     *
     * @var PointInterface
     */
    private $secretKey = null;

    /**
     *
     * @var PublicKeyInterface
     */
    private $recipientKey;

    /**
     *
     * @var PrivateKeyInterface
     */
    private $senderKey;

    /**
     * Initialize a new exchange from a generator point.
     *
     * @param MathAdapterInterface $adapter A math adapter instance.
     * @param \Mdanter\Ecc\Message\MessageFactory $messages A message factory
     */
    public function __construct(MathAdapterInterface $adapter, MessageFactory $messages)
    {
        $this->adapter = $adapter;
        $this->messages = $messages;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\EcDH\EcDHInterface::calculateSharedKey()
     */
    public function calculateSharedKey()
    {
        $this->calculateKey();

        return $this->secretKey->getX();
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\EcDH\EcDHInterface::createMultiPartyKey()
     */
    public function createMultiPartyKey()
    {
        $this->calculateKey();

        return new PublicKey($this->adapter, $this->senderKey->getPoint(), $this->secretKey);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\EcDH\EcDHInterface::setRecipientKey()
     */
    public function setRecipientKey(PublicKeyInterface $key)
    {
        $this->recipientKey = $key;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\EcDH\EcDHInterface::setSenderKey()
     */
    public function setSenderKey(PrivateKeyInterface $key)
    {
        $this->senderKey = $key;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\EcDH\EcDHInterface::encrypt()
     */
    public function encrypt(Message $message)
    {
        $key = hash("sha256", $this->calculateSharedKey(), true);

        $cypherText = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, base64_encode($message->getContent()), MCRYPT_MODE_CBC, $key);
        $message = $this->messages->ciphertext($cypherText);
        return $message;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\EcDH\EcDHInterface::decrypt()
     */
    public function decrypt(EncryptedMessage $ciphertext)
    {
        $key = hash("sha256", $this->calculateSharedKey(), true);

        $clearText = base64_decode(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $ciphertext->getContent(), MCRYPT_MODE_CBC, $key));
        $clearText = $this->messages->plaintext($clearText, 'sha256');
        return $clearText;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\EcDH\EcDHInterface::encryptFile()
     */
    public function encryptFile($path)
    {
        if (file_exists($path) && is_readable($path)) {
            $message = $this->messages->plaintext(file_get_contents($path), 'sha256');
            return $this->encrypt($message);
        }

        throw new \InvalidArgumentException("File '$path' does not exist or is not readable.");
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Crypto\EcDH\EcDHInterface::decryptFile()
     */
    public function decryptFile($path)
    {
        if (file_exists($path) && is_readable($path)) {
            $cipherText = $this->messages->ciphertext(file_get_contents($path));
            return $cipherText;
        }

        throw new \InvalidArgumentException("File '$path' does not exist or is not readable.");
    }

    /**
     *
     */
    private function calculateKey()
    {
        $this->checkExchangeState();

        if ($this->secretKey === null) {
            $this->secretKey = $this->recipientKey->getPoint()->mul($this->senderKey->getSecret());
        }
    }

    /**
     * Verifies that the shared secret is known, or that the required keys are available
     * to calculate the shared secret.
     * @throws \RuntimeException when the exchange has not been made.
     */
    private function checkExchangeState()
    {
        if ($this->secretKey !== null) {
            return;
        }

        if ($this->senderKey === null) {
            throw new \RuntimeException('Sender key not set.');
        }

        if ($this->recipientKey === null) {
            throw new \RuntimeException('Recipient key not set.');
        }
    }
}
