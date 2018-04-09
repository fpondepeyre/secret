<?php

declare(strict_types=1);

namespace Secret\lib;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Class Decrypt service.
 */
class Decrypt
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var Gpg
     */
    private $gpgService;

    /**
     * @var Repository
     */
    private $repositoryService;

    /**
     * @var string
     */
    private $content = '';

    /**
     * Decrypt constructor.
     *
     * @param Filesystem $fs
     * @param Gpg $gpgService
     * @param Repository $repositoryService
     */
    public function __construct(Filesystem $fs, Gpg $gpgService, Repository $repositoryService)
    {
        $this->fs = $fs;
        $this->gpgService = $gpgService;
        $this->repositoryService = $repositoryService;
    }

    /**
     * @param string $project
     * @param string $env
     *
     * @throws \Exception
     *
     * @return null|string
     */
    public function decrypt(string $project, string $env): ?string
    {
        try {
            $file = $this->repositoryService->getSecret($project, $env);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if (!$this->fs->exists($file)) {
            throw new \InvalidArgumentException(sprintf('Unable to find file "%s"', $file));
        }

        if (false === $this->content = $this->gpgService->decryptFile($file)) {
            throw new \RuntimeException(sprintf('Unable to decrypt file %s', $file));

            return null;
        }

        return $this->content;
    }

    /**
     * @param string $path
     *
     * @throws \InvalidArgumentException
     */
    public function dumpFile(string $path): void
    {
        if (empty($this->content)) {
            throw new \InvalidArgumentException('Unable to dump file, empty content');
        }

        $this->fs->dumpFile($path.'/.env', $this->content);
    }
}
