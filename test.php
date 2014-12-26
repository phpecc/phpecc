<?php

require_once "vendor/autoload.php";

use Mdanter\Ecc\EcMath;
use Mdanter\Ecc\EccFactory;

$math = EccFactory::getAdapter();
$g    = EccFactory::getSecgCurves()->generator256k1();
$secret    = 2;

echo "Using private key : ".str_pad($secret, 64, '0', STR_PAD_LEFT)."\n";
echo "Get the public key: ".((new EcMath($secret, $g, $math))->getPoint())."\n";


// Add
$ec = (new EcMath($secret, $g, $math))
    ->add($secret)
    ->mul($g);
echo "Add (prv) : ".$ec->result()."\n";

$ec = (new EcMath($secret, $g, $math))
    ->mul($g)
    ->add($secret);
echo "Add (pub) : ".$ec->result()."\n\n";


// Mod
$ec = (new EcMath($secret, $g, $math))
    ->add($g->getOrder())
    ->mod($g->getOrder());
echo "Mod: ".$ec->result()."\n\n";



// Double
$ec = (new EcMath($secret, $g, $math))
    ->getDouble()
    ->mul($g);
echo "Double (prv) : ".$ec->result()."\n";

$ec = (new EcMath($secret, $g, $math))
    ->mul($g)
    ->getDouble();
echo "Double (pub) : ".$ec->result()."\n\n";




// Multiplication
$ec = (new EcMath($secret, $g, $math))
    ->mul($g)
    ->mul(2);
echo "Mul (prv) : ".$ec->result()."\n";

$ec2 = (new EcMath($secret, $g, $math))
    ->mul(2)
    ->mul($g);
echo "Mul (pub) : ".$ec2->result()."\n\n";

/**
 * I want to replace a block like this:

   if ($this->isPrivate()) {
   // offset + privKey % n
    $key = new PrivateKey(
        str_pad(
            $this->math->decHex(
                $this->math->mod(
                    $this->math->add(
                        $this->math->hexDec($offset->serialize('hex')),
                        $this->getPrivateKey()->serialize('int')
                    ),
                    $this->getGenerator()->getOrder()
                )
            ),
            64,
            '0',
            STR_PAD_LEFT
        )
    );

} else {
    // (offset*G) + (K)
    $key = new PublicKey(
        $this
        // Get the EC point for this offset
        ->getGenerator()
            ->mul(
                $offset->serialize('int')
            )
            // Add it to the public key
            ->add(
                $this->getPublicKey()->getPoint()
            ),
        true
    );
}*/

// Set $offset to '0' to confirm it matches with the first output of this program
$offset = '2';
$pubkey = (new EcMath($secret, $g, $math))->getPoint();

$pub = (new EcMath($pubkey, $g, $math))
    ->add($offset)
    ->mod($g->getOrder());

$prv = (new EcMath($secret, $g, $math))
    ->add($offset)
    ->mod($g->getOrder());

echo "Final pubkey: ".$pub->result()."\n";
echo "Final prvkey: ".$prv->result()."\n";
echo "    ==> pub : ".$prv->getPoint()."\n";