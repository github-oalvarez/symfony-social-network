<?php
namespace AppBundle\Tests\Controller\User;

use AppBundle\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ListControllerTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->client->get('/users');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
    }
}
