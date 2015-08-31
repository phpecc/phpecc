<?php

namespace Mdanter\Ecc\Serializer\Certificates\Extensions;

use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;
use FG\X509\CertificateExtensions;
use Mdanter\Ecc\Crypto\Certificates\CertificateInfo;
use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Util\SigAlgorithmOidMapper;
use Mdanter\Ecc\Util\Hasher;

class RootCA extends AbstractExtensions
{
    private $pubKeySerializer;

    public function __construct(DerPublicKeySerializer $derPubKeySerializer)
    {
        $this->pubKeySerializer = $derPubKeySerializer;
    }

    private function keyIdentifier(Hasher $hasher, PublicKeyInterface $publicKey)
    {
        $binary = $this->pubKeySerializer->serialize($publicKey);
        $hash = $hasher->hash($binary);
        return new OctetString($hash);
    }

    public function apply(CertificateInfo $certificateInfo)
    {
        $caKey = $certificateInfo->getPublicKey();
        $caHasher = SigAlgorithmOidMapper::getHasher($certificateInfo->getSigAlgo());
        $hash = $this->keyIdentifier($caHasher, $caKey);

        $extensions = new CertificateExtensions();
        $extensions->add
        return new Sequence(
            new Sequence(
                new ObjectIdentifier('2.5.29.14'),
                new OctetString(bin2hex($hash->getBinary()))
            ),
            new Sequence(
                new ObjectIdentifier('2.5.29.35'),
                new OctetString(
                    bin2hex((new Sequence($hash))->getBinary())
                )
            ),
            new Sequence(
                new ObjectIdentifier('2.5.29.19'),
                new OctetString(
                    bin2hex((new Sequence(new Boolean(true)))->getBinary())
                )
            )
        );
    }
}