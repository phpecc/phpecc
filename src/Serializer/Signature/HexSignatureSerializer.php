<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Signature;

use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Exception\SignatureDecodeException;
use Mdanter\Ecc\Primitives\CurveFpInterface;

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
     * @throws SignatureDecodeException
     */
    public function serialize(SignatureInterface $signature, CurveFpInterface $curve): string
    {
        return $this->formatter->serialize($signature, $curve);
    }

    /**
     * @param string $binary
     * @return SignatureInterface
     * @throws SignatureDecodeException
     */
    public function parse(string $hex, CurveFpInterface $curve): SignatureInterface
    {
        return $this->parser->parse($hex, $curve);
    }
}
