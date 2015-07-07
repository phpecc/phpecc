<?php

namespace Mdanter\Ecc\Serializer\Certificates;

use FG\X509\CertificateSubject as AsnSubject;
use Mdanter\Ecc\Crypto\Certificates\CertificateSubject;

class CertificateSubjectSerializer
{
    /**
     * @param CertificateSubject $subject
     * @return AsnSubject
     */
    public function toAsn(CertificateSubject $subject)
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

    public function serialize(CertificateSubject $subject)
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