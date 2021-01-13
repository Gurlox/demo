<?php

namespace App\Tests\Unit\Form;

use App\DTO\Form\RegistrationDTO;
use App\Form\RegistrationType;
use App\Tests\Unit\TestCase\ValidationTypeTestCase;

class RegistrationTypeTest extends ValidationTypeTestCase
{
    public function testSubmitValidData(): void
    {
        $email = 'email@test.pl';
        $password = 'password';
        $formData = [
            'email' => $email,
            'password' => [
                'first' => $password,
                'second' => $password,
            ],
        ];

        $request = new RegistrationDTO();
        $form = $this->factory->create(RegistrationType::class, $request);
        $expectedResult = (new RegistrationDTO())->setEmail($email)->setPassword($password);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($request, $expectedResult);
    }
}
