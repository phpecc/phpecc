# v0.4 release notes

v0.4 marks the following updates to the library:

   - Remove the dependency on mcrypt, which is no longer maintained.
   - Avoid multiple PRNG's - only one, provided by random_bytes().
   - Improve the libraries performance by avoiding gmp_strval.
   - String handling for multi-byte systems.
   - Constant time scalar multiplication and signature verification
   - Introduce utilities for point compression, and DER signature serialization.
   - Test each curve with scalar multiplication, EcDH, ECDSA, and RFC6979 test vectors.
   The only outstanding test is RF6979 for secp112r1. 

## Dependency changes

Minimum PHP version is now 5.6.

`symfony/console` requirement was removed.

`ext-mcrypt` requirement was removed.

## Notable changes:

This overview includes changes that affect behaviour, not code moves, refactors, tests, etc.

### New: 

   - [(#99)](https://github.com/phpecc/phpecc/pull/99) [0cb2c74] A `CurveParameters` class is defined to contain curve parameters (size, a, b, prime)
   - [(#105)](https://github.com/phpecc/phpecc/pull/105) [ae6b81d] A `DerSignatureSerializer` was added to the library.
   - [(#135)](https://github.com/phpecc/phpecc/pull/135) [51a54f6] A `CompressedPointSerializer` was added to the library

### API Breaks

   - [(#99)](https://github.com/phpecc/phpecc/pull/99) [0cb2c74] `CurveFp` & `NamedCurveFp` constructors now take a `CurveParameters` instance 
   - [(#106)](https://github.com/phpecc/phpecc/pull/106) [17ff868] The Console application was moved to [phpecc/console](https://github.com/phpecc/console). 
   - [(#123)](https://github.com/phpecc/phpecc/pull/123) [5e9a082] `random_bytes` is now used for random byte generation `RandomGeneratorFactory::getRandomGenerator()`. PHP 5.6 users will use the polyfill provided by `paragonie/random_compat`.
   - [(#126)](https://github.com/phpecc/phpecc/pull/126) [4d44f13] The encryption/decryption API offered by the `EcDH` class was removed. 
   - [(#127)](https://github.com/phpecc/phpecc/pull/127) [cfa03e1] `MathAdapterInterface` is replaced with `GmpMathInterface`. Integers are now handled as `\GMP` instances.
   - [(#128)](https://github.com/phpecc/phpecc/pull/128) [cc13b81] Minimum PHP version is now 5.6.
   - [(#141)](https://github.com/phpecc/phpecc/pull/141) [7ecd070] Remove Mdanter\Ecc\File classes & namespace
   
### Security fixes
 
   - [(#99)](https://github.com/phpecc/phpecc/pull/99) [0cb2c74] The Point multiplication algorithm was improved by padding private keys with zero bits to the bitSize of the curve.
     The result is a constant number of double-add-always iterations per multiplication.
   - [(#114)](https://github.com/phpecc/phpecc/pull/114) [bed7d94] The signature verification algorithm was patched so the final check is carried out using constant-time string 
     comparison.
   - [(#121)](https://github.com/phpecc/phpecc/pull/121) [f6555f1] The `HmacRandomNumberGenerator` was rewritten, and tests now pass for known test vectors.
   - [(#129)](https://github.com/phpecc/phpecc/pull/129) [484d68f] Functions provided by `BinaryString` are used for string operations for safety on multi-byte systems.
  
## Credits

Thanks to everyone who directly contributed to this release:

 - Florent Morselli
 - Matyas Danter
 - Scott Arciszewski
 - Thibaud Fabre
 - Thomas Kerin
