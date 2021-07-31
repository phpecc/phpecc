<?php
namespace Mdanter\Ecc\Serializer\Signature;

use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Primitives\CurveFp;

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

    /**
     * @var CurveFp
     */
    private $curveFp;

    public function __construct(CurveFp $curveFp = null)
    {
        $this->parser = new IEEEP1363\Parser();
        $this->formatter = new IEEEP1363\Formatter();
        $this->curveFp = $curveFp;
    }
    
    /**
     * @param SignatureInterface $signature
     * @param int $curveSize Expected bit length of the R and S
     * @return string
     */
    public function serialize(SignatureInterface $signature, int $curveSize = 0): string
    {
        if ($this->curveFp && !$curveSize) {
            $curveSize = $this->curveFp->getSize();
        }
        return $this->formatter->serialize($signature, $curveSize);
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
