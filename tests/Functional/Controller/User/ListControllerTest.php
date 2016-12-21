<?php
namespace Tests\Functional\Controller\User;

use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\ApiTestCase;

final class ListControllerTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->client->get('/users');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
    }

    public function testGetPagination()
    {
        $this->createUser('Unknown', uniqid());

        for ($i = 0; $i < 25; $i++) {
            $this->createUser('User'.$i, uniqid());
        }

        $response = $this->client->get('/users?filter=user');
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'items[5].name',
            'User5'
        );

        $this->asserter()->assertResponsePropertyEquals($response, 'count', 10);
        $this->asserter()->assertResponsePropertyEquals($response, 'total', 25);
        $this->asserter()->assertResponsePropertyExists($response, '_links.next');

        // page 2
        $nextLink = $this->asserter()->readResponseProperty($response, '_links.next');
        $response = $this->client->get($nextLink);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'items[5].name',
            'User15'
        );
        $this->asserter()->assertResponsePropertyEquals($response, 'count', 10);

        $lastLink = $this->asserter()->readResponseProperty($response, '_links.last');
        $response = $this->client->get($lastLink);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'items[4].name',
            'User24'
        );

        $this->asserter()->assertResponsePropertyDoesNotExist($response, 'items[5].name');
        $this->asserter()->assertResponsePropertyEquals($response, 'count', 5);
    }
}
