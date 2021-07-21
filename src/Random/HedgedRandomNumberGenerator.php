<?php
declare(strict_types=1);
namespace Mdanter\Ecc\Random;

class HedgedRandomNumberGenerator extends HmacRandomNumberGenerator
{
    /**
     * @return string
     * @throws \Exception
     */
    protected function optionalSuffix(): string
    {
        return random_bytes(32);
    }
}
