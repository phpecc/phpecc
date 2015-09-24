<?php

namespace Mdanter\Ecc\Serializer\Signature;

use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;

class DerSignatureSerializer
{
    /**
     * @var Der\Parser
     */
    private $parser;

    /**
     * @var Der\Formatter
     */
    private $formatter;

    /**
     *
     */
    public function __construct()
    {
        $this->parser = new Der\Parser();
        $this->formatter = new Der\Formatter();
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
