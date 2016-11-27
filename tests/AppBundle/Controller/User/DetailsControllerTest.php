<?php
namespace Tests\AppBundle\Controller\User;

use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\ApiTestCase;

final class DetailsControllerTest extends ApiTestCase
{
    public function testGet()
    {
        $user = $this->createUser('Gandalf', 'isitsafe');
        $username = $user->getUsername();

        $response = $this->client->get('/users/'.$username);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
    }
}
