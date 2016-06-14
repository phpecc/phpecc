<?php

require "../vendor/autoload.php";

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

# Same parameters as creating_signature.php

$adapter = EccFactory::getAdapter();
$generator = EccFactory::getNistCurves()->generator384();
$algorithm = 'sha256';
$sigData = base64_decode('MEQCIBe/A2tKKv2ZPEqpjNnh552rEa4NKEIstOF2O3vGG6pAAiB47qyR8FXMTy/ubso8cEjeh4jLPf1nVeErFZyEiNL+Yg==');
$document = 'I am writing today...';

// Parse signature
$sigSerializer = new DerSignatureSerializer();
$sig = $sigSerializer->parse($sigData);

// Parse public key
$keyData = file_get_contents('../tests/data/openssl-pub.pem');
$derSerializer = new DerPublicKeySerializer($adapter);
$pemSerializer = new PemPublicKeySerializer($derSerializer);
$key = $pemSerializer->parse($keyData);

$signer = new Signer($adapter);
$hash = $signer->hashData($generator, $algorithm, $document);
$check = $signer->verify($key, $sig, $hash);

if ($check) {
    echo "Signature verified\n";
} else {
    echo "Signature validation failed\n";
}
