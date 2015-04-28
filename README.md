## Pure PHP Elliptic Curve DSA and DH

[![Build Status](https://travis-ci.org/mdanter/phpecc.svg?branch=master)](https://travis-ci.org/mdanter/phpecc)
[![HHVM Status](http://hhvm.h4cc.de/badge/mdanter/ecc.svg)](http://hhvm.h4cc.de/package/mdanter/ecc)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mdanter/phpecc/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mdanter/phpecc?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mdanter/phpecc/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mdanter/phpecc/?branch=master)

[![Latest Stable Version](https://poser.pugx.org/mdanter/ecc/v/stable.png)](https://packagist.org/packages/mdanter/ecc)
[![Total Downloads](https://poser.pugx.org/mdanter/ecc/downloads.png)](https://packagist.org/packages/mdanter/ecc)
[![Latest Unstable Version](https://poser.pugx.org/mdanter/ecc/v/unstable.png)](https://packagist.org/packages/mdanter/ecc)
[![License](https://poser.pugx.org/mdanter/ecc/license.png)](https://packagist.org/packages/mdanter/ecc)

### Information

This library is a rewrite/update of Matyas Danter's ECC library. All credit goes to him.

For more information on Elliptic Curve Cryptography please read [this fine article](http://www.matyasdanter.com/2010/12/elliptic-curve-php-oop-dsa-and-diffie-hellman/).

### License

This package is released under the MIT license.

### Requirements

* PHP 5.4+
* composer
* ext-gmp
* ext-mcrypt

### Installation

You can install this library via Composer :

`composer require mdanter/ecc`

### Contribute

When sending in pull requests, please make sure to run the `make` command.

The default target runs all PHPUnit and PHPCS tests. All tests
must validate for your contribution to be accepted.

It's also always a good idea to check the results of the [Scrutinizer analysis](https://scrutinizer-ci.com/g/mdanter/phpecc/) for your pull requests.

### Usage

**WARNING** Though this library is tested for compliance to standards, it is subject to at least one documented vulnerability in public-key derivation, which can potentially allow attackers to grab your private keys. **USE AT YOUR OWN RISK**. You've been warned.

**WARNING** All following documentation is based off the master branch, not the tagged versions.

#### Key generation

##### The lazy way

You're in luck, there's a command line tool ! The examples assume that phpecc (found in the bin/ folder) is on your path.

Generate a private/public keypair:

```
$ phpecc genkey --curve=nist-p256 --out=pem
Using curve "nist-p256"
-----BEGIN EC PRIVATE KEY-----
MHYCAQEEHxMDwxsmFiNDNtNXZIfDm7xYlwJU3YedMA3zyhz/0+OgCgYIKoZIzj0D
AQehRANCAATHZZfy/pz9cqrVldcbtM2ucDYahx8IZZWY8/txTGfmwE9VhZDxh2w6
rJruv+3BMOmKqI42MvpuE02U+Rhlf9ch
-----END EC PRIVATE KEY-----
-----BEGIN PUBLIC KEY-----
MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEx2WX8v6c/XKq1ZXXG7TNrnA2Gocf
CGWVmPP7cUxn5sBPVYWQ8YdsOqya7r/twTDpiqiONjL6bhNNlPkYZX/XIQ==
-----END PUBLIC KEY-----
```

Alternately, you can pipe the output to file:

```
$ phpecc genkey --curve=nist-p256 --out=pem > keypair.pem
Using curve "nist-p256"
```

The generated keys *should* be compatible with OpenSSL. However, if you find cases where OpenSSL cannot parse a key generated using `phpecc`, please submit an issue with the parameters used to generate your key.

**Note**: you don't actually need the public key part from the output, it's also encoded in the private key segment.

To get the list of supported curves :

```bash
$ phpecc list-curves
nist-p192
nist-p224
nist-p256
nist-p384
nist-p521
secp256k1
secp384r1
```

##### The developper way

TODO...

#### Asymmetric encryption

##### The dead stupid example:

```php
<?php

require 'vendor/autoload.php';

use \Mdanter\Ecc\EccFactory;
use \Mdanter\Ecc\Message\MessageFactory;

$math = EccFactory::getAdapter();
$generator = EccFactory::getNistCurves()->generator256();

// Yeah, you won't really be doing that...
$alice = $generator->createPrivateKey();
$bob = $generator->createPrivateKey();

$messages = new MessageFactory($math);
$message = $messages->plaintext('Not for eavesdroppers', 'sha256');

// Exchange keys
$aliceDh = $alice->createExchange($messages, $bob->getPublicKey());
$bobDh = $bob->createExchange($messages, $alice->getPublicKey());

$encryptedMessage = $aliceDh->encrypt($message);
$decryptedMessage = $bobDh->decrypt($encryptedMessage);

echo $decryptedMessage->getContent() . PHP_EOL;

```

##### A lesser dead stupid example

A more realistic example, assumes you are Alice, and that your private key is stored (unencrypted) in PEM format on file. You will of course also need Bob's public key in PEM format on file. This example clearly shows that this library can be improved...

You want to encrypt a message for Bob --and only Bob-- to read.

###### Alice encodes the data

```php
<?php

require 'vendor/autoload.php';

use \Mdanter\Ecc\EccFactory;
use \Mdanter\Ecc\File\PemLoader;
use \Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use \Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use \Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use \Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use \Mdanter\Ecc\Message\MessageFactory;

$math = EccFactory::getAdapter();
$messages = new MessageFactory($math);

$loader = new PemLoader();
$privKeySerializer = new PemPrivateKeySerializer(new DerPrivateKeySerializer());
$pubKeySerializer = new PemPublicKeySerializer(new DerPublicKeySerializer());

$alicePrivateKeyPath = '/path/to/alice.priv';
$bobPublicKeyPath = '/path/to/bob.pub';

$alice = $privKeySerializer->parse($loader->loadPrivateKeyData($alicePrivateKeyPath));
$bob = $pubKeySerializer->parse($loader->loadPublicKeyData($bobPublicKeyPath));

$aliceDh = $alice->createExchange($messages, $bob);

$message = $messages->plaintext('To Bob - For your eyes only', 'sha256');
$messageForBob = $aliceDh->encrypt($message);

// Binary!
echo $messageForBob->getContent() . PHP_EOL;

```

Now you can email/snail mail/whatever the encrypted message to Bob, and he will be able to decrypt your secret data (assuming he already has your public key, and his private key...)

###### Bob decodes the encrypted data

```php
<?php

require 'vendor/autoload.php';

use \Mdanter\Ecc\File\PemLoader;
use \Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use \Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use \Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use \Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;

$loader = new PemLoader();
$privKeySerializer = new PemPrivateKeySerializer(new DerPrivateKeySerializer());
$pubKeySerializer = new PemPublicKeySerializer(new DerPublicKeySerializer());

$bobPrivateKeyPath = '/path/to/bob/privkey.pem';
$alicePublicKeyPath = '/path/to/alice/publickey.pem';

$alice = $pubKeySerializer->parse($loader->loadPublicKeyData($alicePrivateKeyPath));
$bob = $privKeySerializer->parse($loader->loadPrivateKeyData($bobPublicKeyPath));

$bobDh = $bob->createExchange($alice);
$messageForBob = $bobDh->decrypt('... the encrypted message... too lazy to actually generate the encoded message');
```
