<?php
namespace AppBundle\Tests\Controller\User;

use AppBundle\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class DetailsControllerTest extends ApiTestCase
{
    public function testGet()
    {
        $user = $this->createUser('Gandalf', 'isitsafe');
        $response = $this->client->get('/users/'.$user->getId());

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
    }
}
