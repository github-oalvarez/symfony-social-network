<?php

namespace AppBundle\Tests\Controller\Api;

use GuzzleHttp\Client;

class UserControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $client = new Client([
            'base_uri' => 'http://localhost:8000',
            'http_errors' => false,
        ]);

        $response = $client->get('/api/users');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
    }
}
