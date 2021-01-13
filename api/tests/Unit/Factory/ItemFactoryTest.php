<?php

namespace App\Tests\Unit\Factory;

use App\DTO\ItemDataDTO;
use App\DTO\ItemDTO;
use App\Entity\Module;
use App\Factory\ItemFactory;
use PHPUnit\Framework\TestCase;

class ItemFactoryTest extends TestCase
{
    public function testCreateFromDTO(): void
    {
        $itemFactory = new ItemFactory();
        $module = $this->createMock(Module::class);

        $childData = new ItemDataDTO();
        $childData->fields = ['field' => 'value'];

        $data = new ItemDataDTO();
        $data->fields = ['parent' => 'value'];

        $childItemDTO = (new ItemDTO())->setName('childName')->setData($childData);
        $itemDTO = (new ItemDTO())->addItem($childItemDTO)->setName('name')->setData($data);

        $item = $itemFactory->createFromDTO($itemDTO, $module);
        $this->assertEquals($item->getModule(), $module);
        $this->assertEquals($item->getName(), $itemDTO->getName());
        $this->assertNotNull($item->getChildren()->first());
    }
}
