<?php

namespace Mdanter\Ecc;

class KeyPair
{
    /**
     *
     * @var PrivateKeyInterface
     */
    private $privateKey;

    /**
     *
     * @var PublicKeyInterface
     */
    private $publicKey;

    /**
     *
     * @param  PrivateKeyInterface       $privateKey
     * @param  PublicKeyInterface        $publicKey
     * @throws \InvalidArgumentException
     */
    public function __construct(PrivateKeyInterface $privateKey = null, PublicKeyInterface $publicKey = null)
    {
        if ($privateKey === null && $publicKey === null) {
            throw new \InvalidArgumentException('At least one key is required.');
        }

        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;

        if ($this->privateKey !== null && $this->publicKey !== null && ! $this->publicKey->equals($privateKey->getPublicKey())) {
            throw new \InvalidArgumentException('Private/public key mismatch.');
        }
    }

    /**
     * @return PrivateKeyInterface
     * @throws \RuntimeException
     */
    public function getPrivateKey()
    {
        if (! $this->hasPrivateKey()) {
            throw new \RuntimeException('Private key not available');
        }

        return $this->privateKey;
    }

    /**
     *
     * @return boolean
     */
    public function hasPrivateKey()
    {
        return $this->privateKey !== null;
    }

    /**
     *
     * @return PublicKeyInterface
     */
    public function getPublicKey()
    {
        if (! $this->hasPublicKey()) {
            $this->publicKey = $this->privateKey->getPublicKey();
        }

        return $this->publicKey;
    }
}
