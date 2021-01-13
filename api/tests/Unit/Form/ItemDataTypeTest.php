<?php

namespace App\Tests\Unit\Form;

use App\DTO\ItemDataDTO;
use App\Form\ItemDataType;
use App\Tests\Unit\TestCase\ValidationTypeTestCase;

class ItemDataTypeTest extends ValidationTypeTestCase
{
    public function testSubmitValidData(): void
    {
        $text = 'someText';
        $style = ['color' => 'white'];
        $formData = [
            'text' => $text,
            'style' => $style,
        ];

        $request = new ItemDataDTO();
        $form = $this->factory->create(ItemDataType::class, $request);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($request->fields['text'], $text);
        $this->assertEquals($request->fields['style'], $style);
    }
}
