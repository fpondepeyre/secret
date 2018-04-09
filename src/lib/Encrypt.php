<?php

declare(strict_types=1);

namespace Secret\lib;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Class Encrypt service.
 */
class Encrypt
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
     * EncryptService constructor.
     *
     * @param Filesystem $fs
     * @param Gpg        $gpgService
     */
    public function __construct(Filesystem $fs, Gpg $gpgService)
    {
        $this->fs = $fs;
        $this->gpgService = $gpgService;
    }

    /**
     * @param string $file
     * @param string $fingerprint
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return string|null
     */
    public function encrypt(string $file, string $fingerprint): ?string
    {
        if (!$this->fs->exists($file)) {
            throw new \InvalidArgumentException(sprintf('Unable to find file "%s"', $file));
        }

        if (false === $content = $this->gpgService->encryptFile($file, $fingerprint)) {
            throw new \RuntimeException('Unable to encrypt file');
            return null;
        }

        return $content;
    }
}
