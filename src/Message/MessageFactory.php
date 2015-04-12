<?php

namespace Mdanter\Ecc\Message;

use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Message\Message;

class MessageFactory
{
    /**
     * @var MathAdapterInterface
     */
    private $adapter;

    /**
     * @param MathAdapterInterface $adapter
     */
    public function __construct(MathAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param $content
     * @param $algo
     * @return Message
     */
    public function plaintext($content, $algo)
    {
        return new Message($this->adapter, $content, $algo);
    }

    /**
     * @param $content
     * @return EncryptedMessage
     */
    public function ciphertext($content)
    {
        return new EncryptedMessage($content);
    }
}
