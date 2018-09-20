<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\Avatar;

use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Domain\User\Persistence\Writer;
use GamePlatform\Framework\Password\PasswordHash;
use GamePlatform\Testing\Traits\CreateAuthToken;
use GamePlatform\Testing\Traits\RunsMigrations;
use GamePlatform\Testing\Traits\UsesContainer;
use GamePlatform\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

class GetControllerIntegrationTest extends TestCase
{
    use UsesHttpServer,
        UsesContainer,
        RunsMigrations,
        CreateAuthToken;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Filesystem */
    private $filesystem;
    /** @var  Writer */
    private $writer;
    /** @var  string */
    private $token;
    /** @var  User */
    private $user;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->filesystem = $this->container->get(Filesystem::class);
        $this->writer = $this->container->get(Writer::class);
        $this->writer->insert(
            $this->user = (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))
                ->setUsername('joesweeny')
                ->setEmail('joe@joe.com')
                ->setPasswordHash(new PasswordHash('password'))
        );
        $this->token = $this->getValidToken($this->container, $this->user->getId());
    }

    public function test_success_response_is_returned_containing_avatar_data()
    {
        $body = (object) [
            'user_id' => (string) $this->user->getId(),
            'filename' => 'avatar.jpg',
            'contents' => base64_encode('file contents encoded')
        ];

        $request = new ServerRequest(
            'put',
            '/api/avatar',
            ['Authorization' => "Bearer {$this->token}"],
            json_encode($body)
        );

        $this->handle($this->container, $request);

        $request = new ServerRequest(
            'get',
            '/api/avatar/' . (string) $this->user->getId(),
            ['Authorization' => "Bearer {$this->token}"]
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $json->status);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals((string) $this->user->getId(), $json->data->avatar->user_id);
        $this->assertEquals('avatar.jpg', $json->data->avatar->filename);
        $this->assertEquals('file contents encoded', $json->data->avatar->contents);

        $this->removeFiles('/avatar/' . (string) $this->user->getId());
    }

    public function test_404_response_is_returned_if_avatar_does_not_exist()
    {
        $request = new ServerRequest(
            'get',
            '/api/avatar/' . (string) $this->user->getId(),
            ['Authorization' => "Bearer {$this->token}"]
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $json->status);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("Avatar with User ID {$this->user->getId()} does not exist", $json->data->errors[0]->message);
    }

    public function test_404_response_is_returned_if_user_id_provided_is_not_a_valid_uuid_string()
    {
        $request = new ServerRequest(
            'get',
            '/api/avatar/not-valid',
            ['Authorization' => "Bearer {$this->token}"]
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $json->status);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Invalid UUID string: not-valid', $json->data->errors[0]->message);
    }

    private function removeFiles(string $file)
    {
        $this->filesystem->delete($file);
        $this->filesystem->deleteDir('/avatar');
    }
}
