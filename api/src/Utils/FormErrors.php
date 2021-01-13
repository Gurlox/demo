<?php

namespace App\Utils;

use Symfony\Component\Form\FormInterface;

class FormErrors
{
    public static function getAll(FormInterface $form): array
    {
        $errors = [];
        foreach ($form as $formElement) {
            $elementName = $formElement->getName();
            foreach ($formElement->getErrors(true) as $error) {
                $errors[$elementName] = $error->getMessage();
            }
        }

        return $errors;
    }
}
