<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 07/07/15
 * Time: 19:11
 */

namespace Mdanter\Ecc\Crypto\Certificates;


class CertificateSubjectFactory
{
    private $subject = [];

    public function country($name)
    {
        $this->subject['country'] = $name;
        return $this;
    }

    public function state($state)
    {
        $this->subject['state'] = $state;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function locality($name)
    {
        $this->subject['locality'] = $name;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function commonName($name)
    {
        $this->subject['commonName'] = $name;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function organization($name)
    {
        $this->subject['organization'] = $name;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function organizationUnit($name)
    {
        $this->subject['organizationUnit'] = $name;
        return $this;
    }

    /**
     * @return CertificateSubject
     */
    public function getSubject()
    {
        return new CertificateSubject($this->subject);
    }
}