<?php

namespace Mdanter\Ecc\Serializer\PublicKey;

use Mdanter\Ecc\PublicKeyInterface;
use PHPASN1\ASN_Sequence;
use PHPASN1\ASN_Integer;
use Mdanter\Ecc\Curves\NamedCurveFp;
use PHPASN1\ASN_ObjectIdentifier;
use PHPASN1\ASN_BitString;
use PHPASN1\ASN_Object;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Curves\SecgCurve;
use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\Math\MathAdapterFactory;
use Mdanter\Ecc\PointInterface;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\GeneratorPoint;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Serializer\PublicKey\Der\Formatter;
use Mdanter\Ecc\Serializer\PublicKey\Der\Parser;

/**
 *
 * @link https://tools.ietf.org/html/rfc5480#page-3
 */
class DerPublicKeySerializer implements PublicKeySerializerInterface
{

    const X509_ECDSA_OID = '1.2.840.10045.2.1';

    /**
     *
     * @var MathAdapterInterface
     */
    private $adapter;

    /**
     *
     * @var Formatter
     */
    private $formatter;

    /**
     *
     * @var Parser
     */
    private $parser;

    /**
     *
     * @param MathAdapterInterface $adapter
     */
    public function __construct(MathAdapterInterface $adapter = null)
    {
        $this->adapter = $adapter ?: MathAdapterFactory::getAdapter();

        $this->formatter = new Formatter($this->adapter);
        $this->parser = new Parser($this->adapter);
    }

    /**
     *
     * @param PublicKeyInterface $key
     * @return string
     */
    public function serialize(PublicKeyInterface $key)
    {
        return $this->formatter->format($key);
    }

    public function getUncompressedKey(PublicKeyInterface $key)
    {
        return $this->formatter->encodePoint($key->getPoint());
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\Serializer\PublicKey\PublicKeySerializerInterface::parse()
     */
    public function parse($string)
    {
        return $this->parser->parse($string);
    }
}
