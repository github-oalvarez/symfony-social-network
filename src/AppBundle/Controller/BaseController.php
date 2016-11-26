<?php
namespace AppBundle\Controller;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct($serializer)
    {
        $this->serializer = $serializer;
    }

    protected function createApiResponse($data, $statusCode = 200)
    {
        $json = $this->serialize($data);
        return new Response($json, $statusCode, array(
            'Content-Type' => 'application/json'
        ));
    }

    protected function serialize($data, $format = 'json')
    {
        return $this->serializer
            ->serialize($data, $format);
    }
}
