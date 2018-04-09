<?php

declare(strict_types=1);

namespace Secret;

use Secret\lib\Decrypt;
use Secret\lib\Gpg;
use Secret\lib\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DecryptCommand.
 */
class DecryptCommand extends Command
{
    protected function configure()
    {
        $this->setName('secret:decrypt')
            ->setDescription('Decrypt secret')
            ->addArgument('project', InputArgument::REQUIRED, 'Choice project')
            ->addArgument('env', InputArgument::REQUIRED, 'Choice env')
            ->addOption('export-path', 'e', InputOption::VALUE_REQUIRED, 'Path where decrypted file will be copy');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $env = $input->getArgument('env');
        $project = $input->getArgument('project');

        $decryptService = new Decrypt(
            new Filesystem(),
            new Gpg(new \gnupg()),
            new Repository(new Filesystem())
        );

        $io->warning(sprintf('Get secret for "%s" with env "%s"', $project, $env));

        try {
            $content = $decryptService->decrypt($project, $env);
            $io->success($content);
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return;
        }

        if (!$exportPath = $input->getOption('export-path')) {
            return;
        }

        if (!$isCopySecret = $io->confirm(sprintf('Do you want to copy this secret to "%s"', $exportPath))) {
            return;
        }

        try {
            $decryptService->dumpFile($exportPath);
            $io->success(sprintf('Copy content to %s', $exportPath));
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }
    }
}
