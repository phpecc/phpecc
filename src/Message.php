<?php

namespace Mdanter\Ecc;

use Mdanter\Ecc\Signature\Signer;
use Mdanter\Ecc\Signature\Signature;

class Message
{

    private $adapter;

    private $message;

    private $algo;

    public function __construct(MathAdapterInterface $adapter, $content, $algo)
    {
        $this->adapter = $adapter;
        $this->content = $content;
        $this->algo = $algo;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getHash($hex = false)
    {
        $hash = hash($this->algo, $this->content, false);

        if ($hex) {
            return $hash;
        }

        return $this->adapter->hexDec($hash);
    }

    public function sign(Signer $signer, PrivateKey $key)
    {
        return $signer->sign($key, $this->getHash(false));
    }

    public function verify(Signer $signer, Signature $signature, PublicKeyInterface $publicKey)
    {
        return $signer->verify($publicKey, $signature, $this->getHash(false));
    }
}