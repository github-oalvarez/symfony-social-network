<?php
namespace AppBundle\Tests\Controller\Api;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

final class UserControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:8000',
            'http_errors' => false,
        ]);
    }

    public function testListUsers()
    {
        $response = $this->client->get('/users');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
    }

    public function testShowUser()
    {
        $userId = 1;
        $response = $this->client->get('/users/' . $userId);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
    }

    public function testNewUser()
    {
        $data = [
            'user' => [
                'name' => 'John Smith',
                'email' => 'john_smith@example.com',
                'password' => '$2a$10$eImiTXuWVxfM37uY4JANjQ',
            ]
        ];

        $response = $this->client->post('/users', [
            'body' => json_encode($data)
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
    }

    public function testNewUserConnection()
    {
        $userId = 1;
        $connectionId = 2;

        $data = [
            'relationship' => [
                'user' => $connectionId
            ]
        ];

        $response = $this->client->post('/users/' . $userId . '/connections', [
            'body' => json_encode($data)
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));

    }
}
