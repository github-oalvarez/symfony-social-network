<?php
namespace AppBundle\Tests\Controller;

use GuzzleHttp\Client;

class TestBase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    public $client;

    public function setUp()
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:8000',
            'http_errors' => false,
        ]);
    }
}
