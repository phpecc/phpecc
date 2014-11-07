## Pure PHP Elliptic Curve DSA and DH

[![Build Status](https://travis-ci.org/mdanter/phpecc.svg?branch=master)](https://travis-ci.org/mdanter/phpecc)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mdanter/phpecc/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mdanter/phpecc?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mdanter/phpecc/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mdanter/phpecc/?branch=master)

### Information

This library is a rewrite/update of Matys Danter's ECC library. All credit goes to him.

For more information on Elliptic Curve Cryptography please read http://matejdanter.com/?cat=14

### License

This package is released under the MIT license.

### Requirements

* PHP 5.4+
* composer
* ext-bcmath or ext-gmp. GMP math is highly recommended due to performance considerations (10x faster than BCMath)

### Installation

You can install this library via Composer :

`composer require mdanter/ecc:~0.1`

### Contribute

When sending in pull requests, please make sure to run the `make` command. 

The default target runs all PHPUnit (for both GMP and BCMath, so you need to install both extensions) and PHPCS tests. All tests
must validate for your contribution to be accepted.

It's also always a good idea to check the results of the [Scrutinizer analysis](https://scrutinizer-ci.com/g/mdanter/phpecc/) for your pull requests. 

### Usage

A very basic encryption scenario :

```php
<?php

require 'vendor/autoload.php';

use \Mdanter\Ecc\EccFactory;

$generator = EccFactory::getNistCurves()->generator192();
$alice = $generator->createKeyExchange();
$bob = $generator->createKeyExchange();

$alice->exchangeKeys($bob);
$encryptedText = $alice->encrypt('Not for eavesdroppers');
$decryptedText = $bob->decrypt($encryptedText);
```
