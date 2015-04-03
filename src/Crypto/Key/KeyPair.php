<?php

namespace Mdanter\Ecc\Crypto\Key;

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
     * @param  PrivateKeyInterface $privateKey
     * @param  PublicKeyInterface  $publicKey
     * @throws \InvalidArgumentException
     */
    public function __construct(PrivateKeyInterface $privateKey = null, PublicKeyInterface $publicKey = null)
    {
        if ($privateKey === null && $publicKey === null) {
            throw new \InvalidArgumentException('At least one key is required.');
        }

        if ($this->privateKey !== null && $this->publicKey !== null && ! $this->publicKey->getPoint()->equals($privateKey->getPublicKey()->getPoint())
        ) {
            throw new \InvalidArgumentException('Private/public key mismatch.');
        }

        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
    }

    /**
     * @return bool
     */
    public function hasPrivateKey()
    {
        return $this->privateKey !== null;
    }

    /**
     * @return bool
     */
    public function hasPublicKey()
    {
        return $this->publicKey !== null;
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
