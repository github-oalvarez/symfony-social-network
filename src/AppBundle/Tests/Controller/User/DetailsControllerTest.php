<?php
namespace AppBundle\Tests\Controller\User;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

final class DetailsControllerTest extends \PHPUnit_Framework_TestCase
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

    public function testGet()
    {
        $userId = 1;
        $response = $this->client->get('/users/'.$userId);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
    }
}
