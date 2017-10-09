phpecc v0.5.0

v0.5.0 is a major release for the library, featuring a full
upgrade to PHP7.

It includes most of the changes found in the `0.4` branch since
it forked off (v0.4.2)

A minor BC break was included in the release - the hashing functions
were extracted from Signer into a new class, SignHasher, which
allows us to fix a bug described in the pull request.

A patch was merged to the ECDH class which should make invalid curve
attacks more difficult to execute. More details can be found on the
pull request. People using ECDH are urged to upgrade to v0.5.0 or
v0.4.4 if PHP5.6 support is required.

A performance improvement was also included, making scalar
multiplication 14% faster.

0.5.0 Change log
================

## PHP version
- #184 `69638eb` PHP7.0 support (removes support for PHP5.6)

## Code cleanup
- #187 `de2fd00` Remove dependency from UncompressedPointSerializer on GmpMath
- #189 `7ee46a7` DerPrivateKeySerializer: take DerPublicKeySerializer as a second parameter
- #208 `02542ff` Updated to use fgrosse/phpasn1 v2.0
- #211 `9cd002a` Minor B.C. break: Signer extract hashing functions into SignHasher
- #212 `70d3e49` Constant size for field element conditional swap
- #218 `8bb28ed` GeneratorPoint::getPublicKeyFrom() - remove unused parameter

## New curve support 
- #190 `fc3768c` add secp-192k1

## Security
- #216 `813c90f` EcDH: check point exists on senderKey's curve

## Tests
- #194 `e9d2f44` FIPS 186-2 and 186-4 test vectors
- #198 `dba8c1b` Add specific test for invalid points
- #202 `c6e44c8` Tests now performed with PHPUnit 6

## Credits

Thanks to everyone who directly contributed to this release:

 - Dylan K. Taylor (dktapps)
 - Johan de Ruijter (johanderuijter)
 - Spomky
 - Thomas Kerin (afk11)
