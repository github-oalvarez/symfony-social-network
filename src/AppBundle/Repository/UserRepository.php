<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use AppBundle\Entity\User;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    public function findById($userId)
    {
        return $this->findOneBy([
            'id' => $userId
        ]);
    }

    public function findUserByUsername($username)
    {
        return $this->findOneBy([
            'username' => $username
        ]);
    }

    public function findUserByEmail($email)
    {
        return $this->findOneBy([
            'email' => $email
        ]);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findAny()
    {
        return $this->createQueryBuilder('u')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function loadUserByUsername($username)
    {
        $user = $this->findUserByUsername($username);

        // allow login by email too
        if (!$user) {
            $user = $this->findUserByEmail($username);
        }

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Email "%s" does not exist.', $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'AppBundle\Entity\User';
    }

    public function findAllQueryBuilder()
    {
        return $this->createQueryBuilder('user');
    }
}
