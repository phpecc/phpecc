<?php

namespace Mdanter\Ecc\Serializer\Signature;

use FG\ASN1\Identifier;
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

    public function serialize(SignatureInterface $signature)
    {
        $sig = $this->toAsn($signature->getR(), $signature->getS());
        return $sig->getBinary();
    }

    public function parse($binary)
    {
        $object = Object::fromBinary($binary);
        echo Identifier::getShortName($object->getType()) . "\n";
        if (!$object->getTypeName() !== 'Sequence') {

        }

        $content = $object->getContent();
        if (count($content) !== 2) {

        }

        list ($r, $s) = $content;

        return new Signature($r->getContent(), $s->getContent());
    }
}