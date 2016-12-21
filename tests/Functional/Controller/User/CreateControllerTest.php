<?php
namespace Tests\Functional\Controller\User;

use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\ApiTestCase;

final class CreateControllerTest extends ApiTestCase
{
    public function testPost()
    {
        $data = [
            'name' => 'John Smith',
            'username' => 'john_smith',
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
