<?php
namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

trait MocksGuzzle
{
    /** @var  Client */
    protected $guzzle;

    protected function mocksGuzzleWithResponses(array $bodies)
    {
        $responses = [];
        foreach ($bodies as $body) {
            $responses[] = new Response(200, [], $body);
        }
        $handler = new MockHandler($responses);

        $this->guzzle = new Client([
                'handler' => HandlerStack::create($handler)
        ]);
    }
}
