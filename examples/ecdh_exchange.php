<?php

require __DIR__ . "/../vendor/autoload.php";

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use Mdanter\Ecc\Util\NumberSize;

// ECDSA domain is defined by curve/generator/hash algorithm,
// which a verifier must be aware of.

$adapter = EccFactory::getAdapter();
$generator = EccFactory::getNistCurves()->generator384();
$useDerandomizedSignatures = true;

$derPub = new DerPublicKeySerializer();
$pemPub = new PemPublicKeySerializer($derPub);
$pemPriv = new PemPrivateKeySerializer(new DerPrivateKeySerializer($adapter, $derPub));

# These .pem and .key are for different keys
$alicePriv = $pemPriv->parse(file_get_contents(__DIR__ . '/../tests/data/openssl-secp256r1.pem'));
$bobPub = $pemPub->parse(file_get_contents(__DIR__ . '/../tests/data/openssl-secp256r1.1.pub.pem'));

$exchange = $alicePriv->createExchange($bobPub);
$shared = $exchange->calculateSharedKey();
echo "Shared secret: " . gmp_strval($shared, 10).PHP_EOL;

# The shared key is never used directly, but used with a key derivation function (KDF)
$kdf = function (GeneratorPoint $G, \GMP $sharedSecret) {
    $adapter = $G->getAdapter();
    $binary = $adapter->intToFixedSizeString(
        $sharedSecret,
        NumberSize::bnNumBytes($adapter, $G->getOrder())
    );

    $hash = hash('sha256', $binary, true);
    return $hash;
};

$key = $kdf($generator, $shared);
echo "Encryption key: " . unpack("H*", $key)[1] . PHP_EOL;
# This key can now be used to encrypt/decrypt messages with the other person
