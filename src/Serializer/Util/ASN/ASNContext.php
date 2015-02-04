<?php

namespace Mdanter\Ecc\Serializer\Util\ASN;

use FG\ASN1\Construct;
use FG\ASN1\Object;

class ASNContext extends Construct
{

    private $contentLength;

    private $identifierOctet;

    public function __construct($identifier, Object $object)
    {
        $this->identifierOctet = $identifier;
        $this->value = array();
        $this->contentLength = $object->getObjectLength();

        $this->addChild($object);
    }

    public function getType()
    {
        return $this->identifierOctet;
    }

    protected function calculateContentLength()
    {
        return $this->contentLength;
    }
}
