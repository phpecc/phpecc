<?php

namespace Mdanter\Ecc\Message;

use Mdanter\Ecc\Math\MathAdapterInterface;

class Message
{
    /**
     * @var MathAdapterInterface
     */
    private $adapter;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $algo;

    /**
     * @param MathAdapterInterface $adapter
     * @param string $content
     * @param string $algo
     */
    public function __construct(MathAdapterInterface $adapter, $content, $algo)
    {
        $this->adapter = $adapter;
        $this->content = $content;
        $this->algo = $algo;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param bool $hex
     * @return int|string
     */
    public function getHash($hex = false)
    {
        $hash = hash($this->algo, $this->content, false);

        return $hex
            ? $hash
            : $this->adapter->hexDec($hash);
    }
}
