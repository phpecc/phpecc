<?php

require "../vendor/autoload.php";

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;

// ECDSA domain is defined by curve/generator/hash algorithm,
// which a verifier must be aware of.

$adapter = EccFactory::getAdapter();
$generator = EccFactory::getNistCurves()->generator384();
$useDerandomizedSignatures = true;

$pemPriv = new PemPrivateKeySerializer(new DerPrivateKeySerializer());
$pemPub = new PemPublicKeySerializer(new DerPublicKeySerializer());

# These .pem and .key are for different keys
$alicePriv = $pemPriv->parse(file_get_contents('../tests/data/openssl-priv.pem'));
$bobPub = $pemPub->parse(file_get_contents('../tests/data/openssl-pub.key'));

$exchange = $alicePriv->createExchange($bobPub);
$shared = $exchange->calculateSharedKey();
echo "Shared secret: " . gmp_strval($shared, 10).PHP_EOL;

# The shared key is never used directly, but used with a key derivation function (KDF)
$kdf = function (GmpMathInterface $math, \GMP $sharedSecret) {
    $binary = $math->intToString($sharedSecret);
    $hash = hash('sha256', $binary, true);
    return $hash;
};

$key = $kdf($adapter, $shared);
echo "Encryption key: " . unpack("H*", $kdf($adapter, $shared))[1] . PHP_EOL;
# This key can now be used to encrypt/decrypt messages with the other person