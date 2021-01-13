<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserFactory
{
    private EncoderFactoryInterface $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function create(string $email, string $password): User
    {
        $password = $this->encoderFactory->getEncoder(User::class)->encodePassword($password, null);
        $user = new User($email, $password);

        return $user;
    }
}
