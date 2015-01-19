<?php

namespace Mdanter\Ecc\Serializer\Util\ASN;

use PHPASN1\ASN_Construct;
use PHPASN1\ASN_Object;

class ASNContext extends ASN_Construct
{

    private $contentLength;
    
    private $identifierOctet;
    
    public function __construct($identifier, ASN_Object $object)
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
