<?php
namespace AppBundle\Serializer;

use AppBundle\Annotation\Link;
use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
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

    public function __construct(UrlGeneratorInterface $urlGenerator, Reader $annotationReader)
    {
        $this->urlGenerator = $urlGenerator;
        $this->annotationReader = $annotationReader;
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
                    $annotation->params
                );
                $links[$annotation->name] = $uri;
            }
        }

        $visitor->setData('_links', $links);
    }

    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.post_serialize',
                'method' => 'onPostSerialize',
                'format' => 'json',
                'class' => 'AppBundle\Entity\User',
            ],
        ];
    }
}
