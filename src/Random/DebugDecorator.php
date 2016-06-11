<?php

namespace Mdanter\Ecc\Random;

class DebugDecorator implements RandomNumberGeneratorInterface
{
    /**
     * @var RandomNumberGeneratorInterface
     */
    private $generator;

    /**
     * @var string
     */
    private $generatorName;

    /**
     * @param RandomNumberGeneratorInterface $generator
     * @param string $name
     */
    public function __construct(RandomNumberGeneratorInterface $generator, $name)
    {
        $this->generator = $generator;
        $this->generatorName = $name;
    }

    /**
     * @param resource|\GMP $max
     * @return resource|\GMP
     */
    public function generate($max)
    {
        echo $this->generatorName.'::rand() = ';

        $result = $this->generator->generate($max);

        echo $result.PHP_EOL;

        return $result;
    }
}
