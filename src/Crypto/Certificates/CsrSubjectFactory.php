<?php

namespace Mdanter\Ecc\Crypto\Certificates;


class CsrSubjectFactory
{
    /**
     * @var array
     */
    private $subject = [];

    /**
     * @param $name
     * @return $this
     */
    public function country($name)
    {
        $this->subject['country'] = $name;
        return $this;
    }

    /**
     * @param $state
     * @return $this
     */
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
     * @return CsrSubject
     */
    public function getSubject()
    {
        return new CsrSubject($this->subject);
    }
}