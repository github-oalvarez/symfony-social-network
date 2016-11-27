<?php
namespace AppBundle\Serializer;

use AppBundle\Annotation\Link;
use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LinkSerializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        Reader $annotationReader
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->annotationReader = $annotationReader;
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /** @var JsonSerializationVisitor $visitor */
        $visitor = $event->getVisitor();

        $object = $event->getObject();
        $annotations = $this->annotationReader
            ->getClassAnnotations(new \ReflectionObject($object));

        $links = [];
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Link) {
                $uri = $this->urlGenerator->generate(
                    $annotation->route,
                    $this->resolveParams($annotation->params, $object)
                );
                $links[$annotation->name] = $uri;
            }
        }

        if ($links) {
            $visitor->setData('_links', $links);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.post_serialize',
                'method' => 'onPostSerialize',
                'format' => 'json',
            ],
        ];
    }

    private function resolveParams(array $params, $object)
    {
        foreach ($params as $key => $param) {
            $params[$key] = $this->expressionLanguage
                ->evaluate($param, ['object' => $object]);
        }

        return $params;
    }
}
