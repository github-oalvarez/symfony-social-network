<?php

require __DIR__.'/vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client(array(
    'base_uri' => 'http://localhost:8000',
    'http_errors' => false,
));

$userId = 1;

$data = array(
    'connectionId' => 2,
);
$response = $client->post('/api/connection/', array(
    'body' => json_encode($data)
));

echo $response->getBody() . "\n\n";
