<?php

use Mdanter\Ecc\EccFactory;

require_once(__DIR__. '/vendor/autoload.php');


$t = microtime(true);

$g = EccFactory::getSecgCurves($math = EccFactory::getAdapter())->generator256k1();
$privKey = "105366245268346348601399826821003822098691517983742654654633135381666943167285";
$privKey = gmp_init($privKey, 10);

for ($i = 0; $i < 160; $i++) {
    $g->mul($privKey);
}

var_dump(microtime(true) - $t);
