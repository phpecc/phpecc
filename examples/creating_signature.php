<?php

require "../vendor/autoload.php";

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

// ECDSA domain is defined by curve/generator/hash algorithm,
// which a verifier must be aware of.

$adapter = EccFactory::getAdapter();
$generator = EccFactory::getNistCurves()->generator384();
$useDerandomizedSignatures = true;
$algorithm = 'sha256';

## You'll be restoring from a key, as opposed to generating one.
$pemSerializer = new PemPrivateKeySerializer(new DerPrivateKeySerializer($adapter));
$keyData = file_get_contents('../tests/data/openssl-priv.pem');
$key = $pemSerializer->parse($keyData);

$document = 'I am writing today...';

$signer = new Signer($adapter);
$hash = $signer->hashData($generator, $algorithm, $document);

# Derandomized signatures are not necessary, but can reduce
# the attack surface for a private key that is to be used often.
if ($useDerandomizedSignatures) {
    $random = \Mdanter\Ecc\Random\RandomGeneratorFactory::getHmacRandomGenerator($key, $hash, $algorithm);
} else {
    $random = \Mdanter\Ecc\Random\RandomGeneratorFactory::getRandomGenerator();
}

$randomK = $random->generate($generator->getOrder());
$signature = $signer->sign($key, $hash, $randomK);

$serializer = new DerSignatureSerializer();
$serializedSig = $serializer->serialize($signature);
echo base64_encode($serializedSig) . PHP_EOL;