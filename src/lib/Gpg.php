<?php

declare(strict_types=1);

namespace Secret\lib;

/**
 * Class Gpg service.
 */
class Gpg
{
    /**
     * GpgService constructor.
     *
     * @param \gnupg $gnupg
     */
    public function __construct(\gnupg $gnupg)
    {
        $this->gnupg = $gnupg;
        $this->gnupg->import(file_get_contents(__DIR__ . '/../../key/public.key'));
        $this->gnupg->import(file_get_contents(__DIR__ . '/../../key/private.key'));
    }

    /**
     * @param string $file
     * @param string $fingerprint
     *
     * @return mixed
     */
    public function encryptFile(string $file, string $fingerprint)
    {
        $this->gnupg->addencryptkey($fingerprint);

        return $this->gnupg->encrypt(file_get_contents($file));
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function decryptFile(string $file)
    {
        return $this->gnupg->decrypt(file_get_contents($file));
    }
}
