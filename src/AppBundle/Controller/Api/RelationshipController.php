<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Relationship;
use AppBundle\Form\RelationshipType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RelationshipController extends Controller
{
    /**
     * @Route("/api/connection")
     * @Method("POST")
     */
    public function newAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $relationship = new Relationship();
        $form = $this->createForm(new RelationshipType(), $relationship);
        $form->submit($data);

//        $relationship->setUser($this->findByUsername('weaverryan'));
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($relationship);
//        $em->flush();

        $response = new Response('It worked. Believe me - I\'m an API', 201);
        $response->headers->set('Location', '/some/programmer/url');

        return $response;
    }

}
