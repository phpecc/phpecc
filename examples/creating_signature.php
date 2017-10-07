<?php

require __DIR__ . "/../vendor/autoload.php";

use Mdanter\Ecc\Crypto\Signature\SignHasher;
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
$keyData = file_get_contents(__DIR__ . '/../tests/data/openssl-secp256r1.pem');
$key = $pemSerializer->parse($keyData);

$document = 'I am writing today...';

$hasher = new SignHasher($algorithm, $adapter);
$hash = $hasher->makeHash($document, $generator);

# Derandomized signatures are not necessary, but is avoids
# the risk of a low entropy RNG, causing accidental reuse
# of a k value for a different message, which leaks the
# private key.
if ($useDerandomizedSignatures) {
    $random = \Mdanter\Ecc\Random\RandomGeneratorFactory::getHmacRandomGenerator($key, $hash, $algorithm);
} else {
    $random = \Mdanter\Ecc\Random\RandomGeneratorFactory::getRandomGenerator();
}
$randomK = $random->generate($generator->getOrder());

$signer = new Signer($adapter);
$signature = $signer->sign($key, $hash, $randomK);

$serializer = new DerSignatureSerializer();
$serializedSig = $serializer->serialize($signature);
echo base64_encode($serializedSig) . PHP_EOL;
