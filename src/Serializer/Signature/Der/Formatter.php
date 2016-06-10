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
            new BignumInt(gmp_strval($signature->getR(), 16)),
            new BignumInt(gmp_strval($signature->getS(), 16))
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
