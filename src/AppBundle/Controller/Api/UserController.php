<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class UserController extends Controller
{
    /**
     * @Route("/api/users", name="api_users_list")
     * @Method("GET")
     */
    public function listAction()
    {
        # TODO move to repository class
        $users = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();

        $data = array('users' => array());
        foreach ($users as $user) {
            $data['users'][] = $this->serializeUser($user);
        }

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/api/users/{userId}")
     * @Method("GET")
     */
    public function showAction($userId)
    {
        # TODO move to repository class -> S from SOLID
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(['id' => $userId]);

        if (!$user) {
            throw $this->createNotFoundException(sprintf(
                'No user found with id "%s"',
                $userId
            ));
        }

        $data = $this->serializeUser($user);

        $response = new Response(json_encode($data), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function serializeUser(User $user)
    {
        return [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ];
    }
}
