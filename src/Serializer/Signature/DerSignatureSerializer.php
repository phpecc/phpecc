<?php

namespace Mdanter\Ecc\Serializer\Signature;

use FG\ASN1\Object;
use FG\ASN1\Universal\Integer;
use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use FG\ASN1\Universal\Sequence;

class DerSignatureSerializer
{
    /**
     * @param int|string $r
     * @param int|string $s
     * @return Sequence
     */
    public function toAsn($r, $s)
    {
        return new Sequence(
            new Integer($r),
            new Integer($s)
        );
    }

    /**
     * @param SignatureInterface $signature
     * @return string
     */
    public function serialize(SignatureInterface $signature)
    {
        return $this->toAsn($signature->getR(), $signature->getS())->getBinary();
    }

    /**
     * @param string $binary
     * @return Signature
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function parse($binary)
    {
        $object = Object::fromBinary($binary);

        if (!$object->getTypeName() !== 'Sequence') {

        }

        $content = $object->getContent();
        if (count($content) !== 2) {

        }

        list ($r, $s) = $content;
        /** @var \FG\ASN1\Universal\Integer $r */
        /** @var \FG\ASN1\Universal\Integer $s */
        return new Signature(
            $r->getContent(),
            $s->getContent()
        );
    }
}