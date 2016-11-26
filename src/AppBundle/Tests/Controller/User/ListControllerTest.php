<?php
namespace AppBundle\Tests\Controller\User;

use AppBundle\Tests\Controller\TestBase;
use Symfony\Component\HttpFoundation\Response;

final class ListControllerTest extends TestBase
{
    public function testGet()
    {
        $response = $this->client->get('/users');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
    }
}
