<?php

namespace App\DataFixtures;

use App\Entity\Flight;
use App\Entity\Seat;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('test');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'qwerty'));
        $manager->persist($user);

        $flight = new Flight();
        $manager->persist($flight);

        $seat1 = new Seat();
        $seat1->setFlight($flight);
        $seat1->setSeatNum(1);
        $manager->persist($seat1);

        $seat2 = new Seat();
        $seat2->setFlight($flight);
        $seat2->setSeatNum(2);
        $manager->persist($seat2);

        $manager->flush();
    }
}
