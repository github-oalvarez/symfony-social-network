<?php
namespace AppBundle\Tests\Controller\User;

use AppBundle\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CreateControllerTest extends ApiTestCase
{
    public function testPost()
    {
        $data = [
            'name' => 'John Smith',
            'email' => 'john_smith@example.com',
            'password' => '$2a$10$eImiTXuWVxfM37uY4JANjQ',
        ];

        $response = $this->client->post('/users', [
            'body' => json_encode($data)
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
    }
}
