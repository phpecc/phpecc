<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Signature\Der;

use FG\ASN1\Exception\ParserException;
use FG\ASN1\Identifier;
use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Mdanter\Ecc\Exception\InvalidSignatureException;

class Parser
{
    /**
     * @param string $binary
     * @return SignatureInterface
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function parse(string $binary): SignatureInterface
    {
        $template = [
            Identifier::SEQUENCE => [
                Identifier::INTEGER,
                Identifier::INTEGER,
            ],
        ];

        $parser = new \FG\ASN1\TemplateParser();
        try {
            $sequence = $parser->parseBinary($binary, $template);
        } catch (ParserException $e) {
            throw new InvalidSignatureException("Invalid ASN.1 for signature", 0, $e);
        } catch (\Exception $e) {
            throw new InvalidSignatureException("Invalid DER for signature", 0, $e);
        }

        return new Signature(
            gmp_init($sequence[0]->getContent(), 10),
            gmp_init($sequence[1]->getContent(), 10)
        );
    }
}
