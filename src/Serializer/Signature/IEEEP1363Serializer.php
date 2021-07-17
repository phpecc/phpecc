<?php
namespace Mdanter\Ecc\Serializer\Signature;

use Mdanter\Ecc\Crypto\Signature\SignatureInterface;

/**
 * Class IEEEP1363Serializer
 * @package Mdanter\Ecc\Serializer\Signature
 */
class IEEEP1363Serializer implements IEEEP1363SerializerInterface
{
    /**
     * @var IEEEP1363\Parser
     */
    private $parser;

    /**
     * @var IEEEP1363\Formatter
     */
    private $formatter;

    public function __construct()
    {
        $this->parser = new IEEEP1363\Parser();
        $this->formatter = new IEEEP1363\Formatter();
    }
    
    /**
     * @param SignatureInterface $signature
     * @return string
     */
    public function serialize(SignatureInterface $signature): string
    {
        return $this->formatter->serialize($signature);
    }

    /**
     * @param string $binary
     * @return SignatureInterface
     */
    public function parse(string $binary): SignatureInterface
    {
        return $this->parser->parse($binary);
    }
}
