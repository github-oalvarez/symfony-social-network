meetup-demo
===========

    * Configure your application in app/config/parameters.yml file.

    * php bin/console doctrine:schema:create
    
    * php bin/console doctrine:fixtures:load

    * Run your application:
        1. Execute the php bin/console server:start command.
        2. Browse to the http://localhost:8000 URL.

# database

http://stackoverflow.com/questions/14753228/implementing-a-friends-list-in-symfony2-1-with-doctrine

    $user = $this->getDoctrine()
                    ->getRepository('AcmeUserBundle:User')
                    ->findOneById($anUserId);
    $friends = $user->getMyFriends();
    $names = array();
    foreach($friends as $friend) $names[] = $friend->getName();

## group mapping

Many-To-Many, Unidirectional

Why are many-to-many associations less common? Because frequently you want to associate additional attributes with an association, in which case you introduce an association class. Consequently, the direct many-to-many association disappears and is replaced by one-to-many/many-to-one associations between the 3 participating classes.

http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-many-unidirectional

## connection mapping

Many-To-Many, Self-referencing¶

http://docs.doctrine-project.org/en/latest/reference/association-mapping.html#many-to-many-self-referencing

# add skeleton
# configuration
# add packages
# create entities
# create fixtures


