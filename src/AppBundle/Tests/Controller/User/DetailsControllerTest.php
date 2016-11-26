<?php
namespace AppBundle\Tests\Controller\User;

use AppBundle\Tests\Controller\TestBase;
use Symfony\Component\HttpFoundation\Response;

final class DetailsControllerTest extends TestBase
{
    public function testGet()
    {
        $userId = 1;
        $response = $this->client->get('/users/'.$userId);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
    }
}
