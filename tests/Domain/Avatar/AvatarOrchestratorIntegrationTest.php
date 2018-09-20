<?php

namespace GamePlatform\Domain\Avatar;

use GamePlatform\Domain\Avatar\Entity\Avatar;
use GamePlatform\Domain\Avatar\Persistence\Repository;
use GamePlatform\Framework\Uuid\Uuid;
use GamePlatform\Testing\Traits\RunsMigrations;
use GamePlatform\Testing\Traits\UsesContainer;
use Interop\Container\ContainerInterface;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

class AvatarOrchestratorIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  AvatarOrchestrator */
    private $orchestrator;
    /** @var  Repository */
    private $repository;
    /** @var  Filesystem */
    private $filesystem;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->repository = $this->container->get(Repository::class);
        $this->filesystem = $this->container->get(Filesystem::class);
        $this->orchestrator = new AvatarOrchestrator(
            $this->repository,
            $this->filesystem
        );
    }

    public function test_avatar_meta_data_is_saved_to_repository_and_file_contents_saved_to_storage()
    {
        $avatar = (new Avatar($id = Uuid::generate(), 'avatar.jpg'))->setFileContents('file contents string');

        $this->assertTrue($this->orchestrator->addAvatar($avatar));
        $this->assertEquals($id, $this->repository->get($id)->getUserId());
        $this->assertTrue($this->filesystem->has('/avatar/' . (string) $id));

        $this->removeFiles('/avatar/' . (string) $id);
    }

    public function test_avatar_can_be_retrieved_from_repository_and_storage()
    {
        $avatar = (new Avatar(Uuid::generate(), 'avatar.jpg'))->setFileContents('file contents string');

        $this->orchestrator->addAvatar($avatar);

        $fetched = $this->orchestrator->getAvatar($avatar->getUserId());

        $this->assertEquals('avatar.jpg', $fetched->getFilename());
        $this->assertEquals('file contents string', $fetched->getFileContents());

        $this->removeFiles('/avatar/' . (string) $fetched->getUserId());
    }

    private function removeFiles(string $file)
    {
        $this->filesystem->delete($file);
        $this->filesystem->deleteDir('/avatar');
    }
}
