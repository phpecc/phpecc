<?php

namespace Mdanter\Ecc\Serializer\Signature;

use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;

class HexSignatureSerializer
{
    /**
     * @var Hex\Parser
     */
    private $parser;

    /**
     * @var Hex\Formatter
     */
    private $formatter;

    /**
     *
     */
    public function __construct()
    {
        $this->parser = new Hex\Parser();
        $this->formatter = new Hex\Formatter();
    }

    /**
     * @param SignatureInterface $signature
     * @return string
     */
    public function serialize(SignatureInterface $signature)
    {
        return $this->formatter->serialize($signature);
    }

    /**
     * @param string $binary
     * @return Signature
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function parse($binary)
    {
        return $this->parser->parse($binary);
    }
}
