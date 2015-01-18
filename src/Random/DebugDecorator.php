<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\RandomNumberGeneratorInterface;

class DebugDecorator implements RandomNumberGeneratorInterface
{
    private $generator;

    private $generatorName;
    
    public function __construct(RandomNumberGeneratorInterface $generator, $name)
    {
        $this->generator = $generator;
        $this->generatorName = $name;
    }
    
    public function generate($max)
    {
        echo $this->generatorName . '::rand() = ';
        
        $result = $this->generator->generate($max);
        
        echo $result . PHP_EOL;
        
        return $result;
    }
}