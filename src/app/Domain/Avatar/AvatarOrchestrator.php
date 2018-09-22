<?php

namespace BackToWin\Domain\Avatar;

use BackToWin\Domain\Avatar\Entity\Avatar;
use BackToWin\Domain\Avatar\Persistence\Repository;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;
use League\Flysystem\Filesystem;

class AvatarOrchestrator
{
    private const DIRECTORY = 'avatar/';

    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Repository $repository, Filesystem $filesystem)
    {
        $this->repository = $repository;
        $this->filesystem = $filesystem;
    }

    public function addAvatar(Avatar $avatar): bool
    {
        $this->persistToRepository($avatar);

        return $this->filesystem->put(self::DIRECTORY . (string) $avatar->getUserId(), $avatar->getFileContents());
    }

    /**
     * @param Uuid $userId
     * @return Avatar
     * @throws NotFoundException
     */
    public function getAvatar(Uuid $userId): Avatar
    {
        $avatar = $this->repository->get($userId);

        if ($file = $this->filesystem->read(self::DIRECTORY . (string) $avatar->getUserId())) {
            $avatar->setFileContents($file);
        }

        return $avatar;
    }

    private function persistToRepository(Avatar $avatar): void
    {
        try {
            $this->repository->update($avatar);
        } catch (NotFoundException $e) {
            $this->repository->insert($avatar);
        }
    }
}
