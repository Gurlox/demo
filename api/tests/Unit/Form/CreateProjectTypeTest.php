<?php

namespace App\Tests\Unit\Form;

use App\DTO\Form\CreateProjectDTO;
use App\Form\CreateProjectType;
use App\Tests\Unit\TestCase\ValidationTypeTestCase;

class CreateProjectTypeTest extends ValidationTypeTestCase
{
    public function testSubmitValidData(): void
    {
        $name = 'name';
        $formData = [
            'name' => $name,
        ];

        $request = new CreateProjectDTO();
        $form = $this->factory->create(CreateProjectType::class, $request);
        $expectedResult = new CreateProjectDTO();
        $expectedResult->name = $name;
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($request, $expectedResult);
    }
}
