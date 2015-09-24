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
            new Integer($signature->getR()),
            new Integer($signature->getS())
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