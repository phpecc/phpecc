<?php

namespace Mdanter\Ecc\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Mdanter\Ecc\File\PemLoader;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use Mdanter\Ecc\File\DerFileLoader;
use Mdanter\Ecc\File\FileLoader;

abstract class AbstractCommand extends Command
{
    /**
     * @param InputInterface $input
     * @param FileLoader $loader
     * @param $fileOptionName
     * @param $dataOptionName
     * @return mixed
     */
    protected function getPrivateKeyData(InputInterface $input, FileLoader $loader, $fileOptionName, $dataOptionName)
    {
        if ($infile = $input->getOption($fileOptionName)) {
            if (! file_exists($infile)) {
                $infile = getcwd().'/'.$infile;
            }

            $data = $loader->loadPrivateKeyData(realpath($infile));
        } else {
            $data = $input->getArgument($dataOptionName);
        }

        return $data;
    }

    /**
     * @param InputInterface $input
     * @param FileLoader $loader
     * @param $fileOptionName
     * @param $dataOptionName
     * @return mixed
     */
    protected function getPublicKeyData(InputInterface $input, FileLoader $loader, $fileOptionName, $dataOptionName)
    {
        if ($infile = $input->getOption($fileOptionName)) {
            if (! file_exists($infile)) {
                $infile = getcwd().'/'.$infile;
            }

            $data = $loader->loadPublicKeyData(realpath($infile));
        } else {
            $data = $input->getArgument($dataOptionName);
        }

        return $data;
    }

    /**
     * @param InputInterface $input
     * @param $formatOptionName
     * @return DerFileLoader|PemLoader
     */
    protected function getLoader(InputInterface $input, $formatOptionName)
    {
        if ($input->getOption($formatOptionName) == 'der') {
            return new DerFileLoader();
        }

        return new PemLoader();
    }

    /**
     * @param InputInterface $input
     * @param $formatOptionName
     * @return DerPrivateKeySerializer|PemPrivateKeySerializer
     */
    protected function getPrivateKeySerializer(InputInterface $input, $formatOptionName)
    {
        $serializer = new DerPrivateKeySerializer();

        if ($input->getOption($formatOptionName) == 'pem') {
            $serializer = new PemPrivateKeySerializer($serializer);
        }

        return $serializer;
    }

    /**
     * @param InputInterface $input
     * @param $formatOptionName
     * @return DerPublicKeySerializer|PemPublicKeySerializer
     */
    protected function getPublicKeySerializer(InputInterface $input, $formatOptionName)
    {
        $serializer = new DerPublicKeySerializer();

        if ($input->getOption($formatOptionName) == 'pem') {
            $serializer = new PemPublicKeySerializer($serializer);
        }

        return $serializer;
    }
}
