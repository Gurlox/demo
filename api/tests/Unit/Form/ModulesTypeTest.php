<?php

namespace App\Tests\Unit\Form;

use App\DTO\ItemDTO;
use App\DTO\ModuleDTO;
use App\DTO\ModulesCollectionDTO;
use App\Form\ModulesType;
use App\Tests\Unit\DataProviders\ModulesListProvider;
use App\Tests\Unit\TestCase\ValidationTypeTestCase;

class ModulesTypeTest extends ValidationTypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = ModulesListProvider::getModulesListSample();

        $request = new ModulesCollectionDTO();
        $form = $this->factory->create(ModulesType::class, $request);
        $form->submit($formData);

        /** @var ModuleDTO $module */
        $module = $request->modules->first();
        /** @var ItemDTO $item */
        $item = $module->getItems()->first();
        /** @var ItemDTO $child */
        $child = $item->getItems()->first();

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($module->getName(), ModulesListProvider::getFirstModuleSample()['name']);
        $this->assertEquals($item->getName(), ModulesListProvider::getFirstItemSample()['name']);
        $this->assertEquals($child->getName(), ModulesListProvider::getFirstChildItemSample()['name']);
    }
}
