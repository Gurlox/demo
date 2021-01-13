<?php

namespace App\Tests\Unit\Form;

use App\DTO\ItemDTO;
use App\DTO\ModuleDTO;
use App\Form\ItemType;
use App\Tests\Unit\DataProviders\ModulesListProvider;
use App\Tests\Unit\TestCase\ValidationTypeTestCase;

class ItemTypeTest extends ValidationTypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = ModulesListProvider::getFirstItemSample();

        $request = new ItemDTO();
        $form = $this->factory->create(ItemType::class, $request);
        $form->submit($formData);

        /** @var ItemDTO $child */
        $child = $request->getItems()->first();

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($request->getName(), $formData['name']);
        $this->assertEquals($request->getData()->fields, $formData['data']);
        $this->assertEquals($child->getName(), ModulesListProvider::getFirstChildItemSample()['name']);
    }
}
