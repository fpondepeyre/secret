<?php

declare(strict_types=1);

namespace Secret\lib;

use Gitonomy\Git\Admin;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class Repository service.
 */
class Repository
{
    const GIT_REPOSITORY = 'git@github.com:fpondepeyre/vault.git';

    const TMP_REPOSITORY_DIR = '/tmp/gitlib';

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * RepositoryService constructor.
     *
     * @param Filesystem $fs
     */
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param $project
     * @param $env
     *
     * @return string
     */
    public function getSecret($project, $env): string
    {
        $this->fs->remove(self::TMP_REPOSITORY_DIR);
        Admin::cloneTo(self::TMP_REPOSITORY_DIR, self::GIT_REPOSITORY, false);

        $file = sprintf('%s/%s/%s/.env', self::TMP_REPOSITORY_DIR, $project, $env);
        if (!$this->fs->exists($file)) {
            throw new \InvalidArgumentException(sprintf('Unable to find secret "%s"', $file));
        }

        return $file;
    }
}
