<?php

namespace App\Tests\Unit\Factory;

use App\DTO\ItemDTO;
use App\DTO\ModuleDTO;
use App\Entity\Item;
use App\Entity\Module;
use App\Entity\Page;
use App\Factory\ItemFactory;
use App\Factory\ModuleFactory;
use PHPUnit\Framework\TestCase;

class ModuleFactoryTest extends TestCase
{
    public function testCreateFromDTO(): void
    {
        $page = $this->createMock(Page::class);
        $itemDTO = new ItemDTO();
        $moduleDTO = (new ModuleDTO())
            ->setName('name')
            ->setShowInMenu(true)
            ->setSlug('slug')
            ->setLabelInMenu('label')
            ->setType(Module::TYPE_BOXES)
            ->addItem($itemDTO)
        ;

        $item = $this->createMock(Item::class);
        $itemFactory = $this->createMock(ItemFactory::class);
        $itemFactory->method('createFromDTO')->willReturn($item);
        $moduleFactory = new ModuleFactory($itemFactory);

        $result = $moduleFactory->createFromDTO($moduleDTO, $page);
        $this->assertEquals($result->getItems()->first(), $item);
        $this->assertEquals($result->getName(), $moduleDTO->getName());
        $this->assertEquals($result->getPage(), $page);
        $this->assertEquals($moduleDTO->isShowInMenu(), $moduleDTO->isShowInMenu());
        $this->assertEquals($result->getSlug(), $moduleDTO->getSlug());
        $this->assertEquals($result->getLabelInMenu(), $moduleDTO->getLabelInMenu());
    }
}
