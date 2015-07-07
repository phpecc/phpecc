<?php

namespace Mdanter\Ecc\Crypto\Certificates;


class CsrSubject
{
    /**
     * @var array
     */
    private $values = [];

    /**
     * @param array $subjectValues
     */
    public function __construct(array $subjectValues)
    {
        $this->values = $subjectValues;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param $key
     * @return null
     */
    public function value($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }

        return null;
    }
}