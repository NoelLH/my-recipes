<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUsers implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = (new User())
            ->setUsername('user')
            ->setPassword('test')
            ->setEmail('noel@noellh.com');

        $manager->persist($user);
        $manager->flush();
    }
}
