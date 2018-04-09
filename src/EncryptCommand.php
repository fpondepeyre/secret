<?php

declare(strict_types=1);

namespace Secret;

use Secret\lib\Encrypt;
use Secret\lib\Gpg;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class EncryptCommand.
 */
class EncryptCommand extends Command
{
    protected function configure()
    {
        $this->setName('secret:encrypt')
            ->setDescription('Encrypt a file')
            ->addArgument('file', InputArgument::REQUIRED, 'File path to encrypt')
            ->addArgument('fingerprint', InputArgument::REQUIRED, 'A finger print');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $file = $input->getArgument('file');
        $fingerprint = $input->getArgument('fingerprint');

        $encryptService = new Encrypt(new Filesystem(), new Gpg(new \gnupg()));

        try {
            $content = $encryptService->encrypt($file, $fingerprint);
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return;
        }

        $io->success($content);
    }
}
