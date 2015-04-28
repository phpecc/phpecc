<?php

namespace Mdanter\Ecc\Message;


class EncryptedMessage
{
    /**
     * @var string
     */
    private $content;

    /**
     * @param string $byteString
     */
    public function __construct($byteString)
    {
        $this->content = $byteString;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
}