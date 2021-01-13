<?php

namespace App\Tests\Unit\Factory;

use App\Entity\User;
use App\Factory\UserFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class UserFactoryTest extends TestCase
{
    /**
     * @return MockObject|EncoderFactoryInterface
     */
    private function mockEncoder(): MockObject
    {
        $encoderFactoryInterface = $this->createMock(EncoderFactoryInterface::class);
        $passwordEncoderInterface = $this->createMock(PasswordEncoderInterface::class);
        $passwordEncoderInterface->method('encodePassword')->willReturn('hash');
        $encoderFactoryInterface->method('getEncoder')->willReturn($passwordEncoderInterface);

        return $encoderFactoryInterface;
    }

    public function testCreate()
    {
        $email = 'email@test.pl';
        $password = 'pass';

        $user = new User($email, 'hash');
        $userFactory = new UserFactory($this->mockEncoder());
        $result = $userFactory->create($email, $password);
        $this->assertEquals($user->getEmail(), $result->getEmail());
        $this->assertEquals($user->getPassword(), $result->getPassword());
    }

    public function testCreateWrongEmail()
    {
        $userFactory = new UserFactory($this->mockEncoder());
        $this->expectException(\InvalidArgumentException::class);
        $userFactory->create('thisIsNotValidMail', 'pass');
    }
}
