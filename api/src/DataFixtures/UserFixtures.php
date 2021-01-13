<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserFixtures extends Fixture
{
    const USER_1 = [
        'password' => 'password',
        'email' => 'test@example.pl',
    ];
    const USER_2 = [
        'password' => 'password',
        'email' => 'test2@example.pl',
    ];

    private EncoderFactoryInterface $encoderFactory;

    private UserFactory $userFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory, UserFactory $userFactory)
    {
        $this->encoderFactory = $encoderFactory;
        $this->userFactory = $userFactory;
    }

    public function load(ObjectManager $manager): void
    {
        $counter = 1;
        foreach ((new \ReflectionClass(UserFixtures::class))->getConstants() as $key => $element) {
            $manager->persist($this->createUser($counter));
            $counter++;
        }
        $manager->flush();
    }

    private function createUser(int $number): User
    {
        $user = $this->userFactory->create(
            constant('self::USER_'.$number)['email'],
            constant('self::USER_'.$number)['password']
        );
        $this->addReference('user'.$number, $user);

        return $user;
    }
}
