<?php

namespace Mdanter\Ecc\Serializer\PublicKey;

use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Math\MathAdapterFactory;
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
     * @var GmpMathInterface
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
     * @param GmpMathInterface $adapter
     */
    public function __construct(GmpMathInterface $adapter = null)
    {
        $this->adapter = $adapter ?: MathAdapterFactory::getAdapter();

        $this->formatter = new Formatter($this->adapter);
        $this->parser = new Parser($this->adapter);
    }

    /**
     *
     * @param  PublicKeyInterface $key
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
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Serializer\PublicKey\PublicKeySerializerInterface::parse()
     */
    public function parse($string)
    {
        return $this->parser->parse($string);
    }
}
