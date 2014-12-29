<?php

namespace Mdanter\Ecc;


interface EcMathInterface {
    public function add($addend);
    public function mul($multiplicand);
    public function getDouble();
    public function mod($int);
    public function cmp($n);
    public function result();
}