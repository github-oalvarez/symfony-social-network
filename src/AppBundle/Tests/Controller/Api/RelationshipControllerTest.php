<?php

namespace AppBundle\Tests\Controller\Api;

use AppBundle\Entity\User;
use GuzzleHttp\Client;

class RelationshipControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testPOST()
    {
        $client = new Client([
            'base_uri' => 'http://localhost:8000',
            'http_errors' => false,
        ]);

        $user = new User();
        $connection = new User();

        $data = array(
            'user' => $user,
            'connection' => $connection,
        );

        $response = $client->post('/api/connection', [
            'body' => json_encode($data)
        ]);

        echo $response->getBody();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $finishedData = json_decode($response->getBody());
        $this->assertArrayHasKey('user', $finishedData);
    }
}
