<?php

namespace Mdanter\Ecc\Serializer\Signature\Der;

use FG\ASN1\Identifier;
use Mdanter\Ecc\Crypto\Signature\Signature;

class Parser
{
    /**
     * @param string $binary
     * @return Signature
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function parse($binary)
    {
        $object = BignumObjectParser::fromBinary($binary);
        if ($object->getType() !== Identifier::SEQUENCE) {
            throw new \RuntimeException('Failed to parse signature');
        }

        $content = $object->getContent();
        if (count($content) !== 2) {
            throw new \RuntimeException('Failed to parse signature');
        }

        /** @var \FG\ASN1\Universal\Integer $r  */
        /** @var \FG\ASN1\Universal\Integer $s  */
        list ($r, $s) = $content;
        if ($r->getType() !== Identifier::INTEGER || $s->getType() !== Identifier::INTEGER) {
            throw new \RuntimeException('Failed to parse signature');
        }

        return new Signature(
            $r->getContent(),
            $s->getContent()
        );
    }
}
