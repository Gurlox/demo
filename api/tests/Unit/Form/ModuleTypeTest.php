<?php

namespace App\Tests\Unit\Form;

use App\DTO\ItemDTO;
use App\DTO\ModuleDTO;
use App\Form\ModuleType;
use App\Tests\Unit\DataProviders\ModulesListProvider;
use App\Tests\Unit\TestCase\ValidationTypeTestCase;

class ModuleTypeTest extends ValidationTypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = ModulesListProvider::getFirstModuleSample();

        $request = new ModuleDTO();
        $form = $this->factory->create(ModuleType::class, $request);
        $form->submit($formData);

        /** @var ItemDTO $item */
        $item = $request->getItems()->first();
        /** @var ItemDTO $child */
        $child = $item->getItems()->first();

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($request->getName(), $formData['name']);
        $this->assertEquals($item->getName(), ModulesListProvider::getFirstItemSample()['name']);
        $this->assertEquals($child->getName(), ModulesListProvider::getFirstChildItemSample()['name']);
    }
}
