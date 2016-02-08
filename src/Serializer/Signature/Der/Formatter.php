<?php

namespace Mdanter\Ecc\Serializer\Signature\Der;

use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\Sequence;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;

class Formatter
{
    /**
     * @param SignatureInterface $signature
     * @return Sequence
     */
    public function toAsn(SignatureInterface $signature)
    {
        return new Sequence(
            new BignumInt($signature->getR()),
            new BignumInt($signature->getS())
        );
    }

    /**
     * @param SignatureInterface $signature
     * @return string
     */
    public function serialize(SignatureInterface $signature)
    {
        return $this->toAsn($signature)->getBinary();
    }
}
