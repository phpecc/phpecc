<?php

namespace Mdanter\Ecc\Serializer\Certificates;

use FG\X509\CertificateSubject as AsnSubject;
use Mdanter\Ecc\Crypto\Certificates\CsrSubject;

class CsrSubjectSerializer
{
    /**
     * @param CsrSubject $subject
     * @return AsnSubject
     */
    public function toAsn(CsrSubject $subject)
    {
        return new AsnSubject(
            $subject->value('commonName'),
            $subject->value('email'),
            $subject->value('organization'),
            $subject->value('locality'),
            $subject->value('state'),
            $subject->value('country'),
            $subject->value('organizationUnit')
        );
    }

    public function serialize(CsrSubject $subject)
    {
        $asn = new AsnSubject(
            $subject->value('commonName'),
            $subject->value('email'),
            $subject->value('organization'),
            $subject->value('locality'),
            $subject->value('state'),
            $subject->value('country'),
            $subject->value('organizationUnit')
        );

        return $asn->getBinary();
    }
}