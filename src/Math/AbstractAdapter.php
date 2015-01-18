<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\RandomNumberGeneratorInterface;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Random\RandomGeneratorFactory;

abstract class AbstractAdapter implements MathAdapterInterface
{
    private $rngAdapter;

    /**
     * 
     * @param RandomNumberGeneratorInterface $generator
     */
    public function __construct(RandomNumberGeneratorInterface $generator = null)
    {
        $this->rngAdapter = $generator ?: RandomGeneratorFactory::getRandomGenerator();
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\MathAdapterInterface::rand()
     */
    public function rand($max)
    {
        return $this->rngAdapter->generate($max);
    }
    
    /**
     * 
     * @return RandomNumberGeneratorInterface
     */
    protected function getRandomGenerator()
    {
        return $this->rngAdapter;
    }
}