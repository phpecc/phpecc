<?php

namespace Mdanter\Ecc\Serializer\PublicKey\Der;

use FG\ASN1\Object;
use FG\ASN1\Universal\Sequence;
use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Point\PointSerializerInterface;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Crypto\Key\PublicKey;

class Parser
{

    /**
     * @var GmpMathInterface
     */
    private $adapter;

    /**
     * @var UncompressedPointSerializer
     */
    private $pointSerializer;

    /**
     * Parser constructor.
     * @param GmpMathInterface $adapter
     * @param PointSerializerInterface|null $pointSerializer
     */
    public function __construct(GmpMathInterface $adapter, PointSerializerInterface $pointSerializer = null)
    {
        $this->adapter = $adapter;
        $this->pointSerializer = $pointSerializer ?: new UncompressedPointSerializer($adapter);
    }

    /**
     * @param string $binaryData
     * @return PublicKey
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function parse($binaryData)
    {
        $asnObject = Object::fromBinary($binaryData);

        if (! ($asnObject instanceof Sequence) || $asnObject->getNumberofChildren() != 2) {
            throw new \RuntimeException('Invalid data.');
        }

        $children = $asnObject->getChildren();

        $oid = $children[0]->getChildren()[0];
        $curveOid = $children[0]->getChildren()[1];
        $encodedKey = $children[1];

        if ($oid->getContent() !== DerPublicKeySerializer::X509_ECDSA_OID) {
            throw new \RuntimeException('Invalid data: non X509 data.');
        }

        $generator = CurveOidMapper::getGeneratorFromOid($curveOid);

        return $this->parseKey($generator, $encodedKey->getContent());
    }

    /**
     * @param GeneratorPoint $generator
     * @param $data
     * @return PublicKey
     */
    public function parseKey(GeneratorPoint $generator, $data)
    {
        $point = $this->pointSerializer->unserialize($generator->getCurve(), $data);

        return new PublicKey($this->adapter, $generator, $point);
    }
}
