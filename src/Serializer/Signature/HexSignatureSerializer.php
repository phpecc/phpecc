<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Signature;

use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Exception\SignatureDecodeException;

class HexSignatureSerializer implements HexSignatureSerializerInterface
{
    /**
     * @var Hex\Parser
     */
    private $parser;

    /**
     * @var Hex\Formatter
     */
    private $formatter;

    public function __construct()
    {
        $this->parser = new Hex\Parser();
        $this->formatter = new Hex\Formatter();
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
     * @throws SignatureDecodeException
     */
    public function parse(string $hex): SignatureInterface
    {
        return $this->parser->parse($hex);
    }
}
