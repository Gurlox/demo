<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Exception\FormValidationException;
use App\Factory\UserFactory;
use App\Service\UserService;
use App\Tests\Unit\TestCase\ValidationTypeTestCase;
use Doctrine\ORM\EntityManagerInterface;

class UserServiceTest extends ValidationTypeTestCase
{
    public function testRegister(): void
    {
        $email = 'test@test.pl';
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($email);
        $userFactory = $this->createMock(UserFactory::class);
        $userFactory->method('create')->willReturn($user);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist')->willReturn(null);
        $em->method('flush')->willReturn(null);

        $userService = new UserService($this->factory, $userFactory, $em);
        $userDTO = $userService->register($this->getRegistrationFormData('email@test.pl', 'password'));

        $this->assertEquals($userDTO->getEmail(), $email);

        $this->expectException(FormValidationException::class);
        $userService->register($this->getRegistrationFormData(
            'email@test.pl',
            'password',
            'notTheSame'
        ));
    }

    private function getRegistrationFormData(string $email, string $password, ?string $passwordInvalid = null): array
    {
        return [
            'email' => $email,
            'password' => [
                'first' => $password,
                'second' => $passwordInvalid ?? $password,
            ],
        ];
    }
}
