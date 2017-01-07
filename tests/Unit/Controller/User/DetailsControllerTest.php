<?php
namespace Tests\Unit\Controller\User;

use PHPUnit\Framework\TestCase;
use Tests\Unit\MocksGuzzle;

final class DetailsControllerTest extends TestCase
{
    use MocksGuzzle;

    public function setUp()
    {
        $user = json_encode([
            'user' => [
                'name' => 'Ericka Williamson',
                'username' => 'quinton31',
                'email' => 'ephraim21@cummings.com',
                '_links' => [
                    'self' => '/users/quinton31'
                ]
            ]
        ]);

        $this->mocksGuzzleWithResponses([$user]);
    }

    public function testGet() {
        $response = $this->guzzle->get('/users/quinton31');
        $this->assertEquals(200, $response->getStatusCode());
        print_r($response->getBody()->getContents());
    }
}
