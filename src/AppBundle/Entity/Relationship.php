<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table("relationships")
 * @UniqueEntity(fields = {"user", "connection"})
 */
final class Relationship
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="connections")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="connectionsWithMe")
     */
    private $connection;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $accepted;

    public function __construct()
    {
        $this->accepted = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    public function hasAccepted()
    {
        return $this->accepted === true;
    }
}
