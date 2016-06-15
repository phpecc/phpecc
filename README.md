## Pure PHP Elliptic Curve DSA and DH

[![Build Status](https://travis-ci.org/phpecc/phpecc.svg?branch=master)](https://travis-ci.org/phpecc/phpecc)
[![HHVM Status](http://hhvm.h4cc.de/badge/mdanter/ecc.svg)](http://hhvm.h4cc.de/package/mdanter/ecc)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpecc/phpecc/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phpecc/phpecc?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/phpecc/phpecc/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phpecc/phpecc/?branch=master)

[![Latest Stable Version](https://poser.pugx.org/mdanter/ecc/v/stable.png)](https://packagist.org/packages/mdanter/ecc)
[![Total Downloads](https://poser.pugx.org/mdanter/ecc/downloads.png)](https://packagist.org/packages/mdanter/ecc)
[![Latest Unstable Version](https://poser.pugx.org/mdanter/ecc/v/unstable.png)](https://packagist.org/packages/mdanter/ecc)
[![License](https://poser.pugx.org/mdanter/ecc/license.png)](https://packagist.org/packages/mdanter/ecc)

### Information

This library is a rewrite/update of Matyas Danter's ECC library. All credit goes to him.

For more information on Elliptic Curve Cryptography please read [this fine article](http://www.matyasdanter.com/2010/12/elliptic-curve-php-oop-dsa-and-diffie-hellman/).

The library supports the following curves:

 - secp112r1
 - secp256k1
 - nistp192
 - nistp224
 - nistp256 / secg256r1
 - nistp384 / secg384r1
 - nistp521

The library exposes a class for random byte generation, for PHP7+ users is provided by `random_bytes`. 
PHP5.6 users will use the paragonie/random_bytes polyfill. 

During ECDSA, a random value `k` is required. It is acceptable to use a true RNG to generate this value, but 
should the same `k` value ever be repeatedly used for a key, an attacker can recover that signing key. 
The HMAC random generator can derive a deterministic k value from the message hash and private key, voiding
this concern.

The library uses a non-branching Montgomery ladder for scalar multiplication, as it's constant time and avoids secret 
dependant branches. 
 
### License

This package is released under the MIT license.

### Requirements

* PHP 5.6+
* composer
* ext-gmp

### Installation

You can install this library via Composer :

`composer require mdanter/ecc`

### Contribute

When sending in pull requests, please make sure to run the `make` command.

The default target runs all PHPUnit and PHPCS tests. All tests
must validate for your contribution to be accepted.

It's also always a good idea to check the results of the [Scrutinizer analysis](https://scrutinizer-ci.com/g/phpecc/phpecc/) for your pull requests.

### Usage

#### Key generation

```php
<?php

require "../vendor/autoload.php";

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;

$adapter = EccFactory::getAdapter();
$generator = EccFactory::getNistCurves()->generator384();
$private = $generator->createPrivateKey();

$keySerializer = new PemPrivateKeySerializer(new DerPrivateKeySerializer($adapter));
$data = $keySerializer->serialize($private);
echo $data.PHP_EOL;
```

#### ECDSA - Signature creation
```php
<?php

require "../vendor/autoload.php";

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\File\PemLoader;
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
$pemLoader = new PemLoader();
$keyData = $pemLoader->loadPrivateKeyData('../tests/data/openssl-priv.pem');
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
```php

#### ECDSA - Signature verification

```php
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

```

#### Asymmetric encryption

```php
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
```