<?php

namespace Tests\ApiBundle\Utils;
use App\Utils\FormErrors;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Test\TypeTestCase;

class FormErrorsTest extends TypeTestCase
{
    public function testReturnAllFormErrors(): void
    {
        $form = $this->factory->createBuilder()
            ->add('field', TextType::class)
            ->getForm();

        $form->get('field')->addError(new FormError('form error'));

        $errors = FormErrors::getAll($form);
        $this->assertArrayHasKey('field', $errors);
        $this->assertEquals('form error', $errors['field']);
    }
}
