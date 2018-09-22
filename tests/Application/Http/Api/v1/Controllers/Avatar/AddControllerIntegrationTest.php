<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\Avatar;

use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\User\Persistence\Writer;
use BackToWin\Framework\Password\PasswordHash;
use BackToWin\Framework\Uuid\Uuid;
use BackToWin\Testing\Traits\CreateAuthToken;
use BackToWin\Testing\Traits\RunsMigrations;
use BackToWin\Testing\Traits\UsesContainer;
use BackToWin\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

class AddControllerIntegrationTest extends TestCase
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

    public function test_success_response_is_avatar_data_is_persisted_correctly()
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

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $json->status);
        $this->assertEquals(200, $response->getStatusCode());

        $this->removeFiles('/avatar/' . (string) $this->user->getId());
    }

    public function test_400_response_is_returned_if_required_properties_are_missing_from_request_body()
    {
        $body = (object) [
            'user_id' => (string) $this->user->getId(),
        ];

        $request = new ServerRequest(
            'put',
            '/api/avatar',
            ['Authorization' => "Bearer {$this->token}"],
            json_encode($body)
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Required field 'filename' is missing", $json->data->errors[0]->message);
        $this->assertEquals("Required field 'contents' is missing", $json->data->errors[1]->message);
    }

    public function test_403_response_is_returned_if_attempting_to_add_an_avatar_for_user_that_does_not_own_avatar()
    {
        $body = (object) [
            'user_id' => (string) Uuid::generate(),
            'filename' => 'avatar.jpg',
            'contents' => base64_encode('file contents encoded')
        ];

        $request = new ServerRequest(
            'put',
            '/api/avatar',
            ['Authorization' => "Bearer {$this->token}"],
            json_encode($body)
        );

        $response = $this->handle($this->container, $request);

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals('You are not authenticated to update this resource', $json->data->errors[0]->message);
    }

    private function removeFiles(string $file)
    {
        $this->filesystem->delete($file);
        $this->filesystem->deleteDir('/avatar');
    }
}
