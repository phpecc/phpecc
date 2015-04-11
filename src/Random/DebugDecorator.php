<?php

namespace Mdanter\Ecc\Random;

class DebugDecorator implements RandomNumberGeneratorInterface
{
    /**
     * @var RandomNumberGeneratorInterface
     */
    private $generator;

    /**
     * @var
     */
    private $generatorName;

    /**
     * @param RandomNumberGeneratorInterface $generator
     * @param $name
     */
    public function __construct(RandomNumberGeneratorInterface $generator, $name)
    {
        $this->generator = $generator;
        $this->generatorName = $name;
    }

    /**
     * @param int|string $max
     * @return mixed
     */
    public function generate($max)
    {
        echo $this->generatorName.'::rand() = ';

        $result = $this->generator->generate($max);

        echo $result.PHP_EOL;

        return $result;
    }
}
