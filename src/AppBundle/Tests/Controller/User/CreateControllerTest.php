<?php
namespace AppBundle\Tests\Controller\User;

use AppBundle\Tests\Controller\TestBase;
use Symfony\Component\HttpFoundation\Response;

final class CreateControllerTest extends TestBase
{
    public function testPost()
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
}
