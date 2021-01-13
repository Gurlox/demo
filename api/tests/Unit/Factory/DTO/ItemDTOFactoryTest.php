<?php

namespace App\Tests\Unit\Factory\DTO;

use App\Entity\Item;
use App\Factory\DTO\ItemDTOFactory;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ItemDTOFactoryTest extends TestCase
{
    public function testCreateFromItem(): void
    {
        $childName = 'childName';
        $childData = ['childData'];
        $child = $this->createMock(Item::class);
        $child->method('getName')->willReturn($childName);
        $child->method('getData')->willReturn($childData);
        $child->method('getChildren')->willReturn(new ArrayCollection());

        $children = new ArrayCollection();
        $children->add($child);

        $name = 'name';
        $data = ['data'];
        $item = $this->createMock(Item::class);
        $item->method('getName')->willReturn($name);
        $item->method('getData')->willReturn($data);
        $item->method('getChildren')->willReturn($children);

        $itemDTOFactory = new ItemDTOFactory();
        $result = $itemDTOFactory->createFromItem($item);

        $this->assertEquals($result->getName(), $name);
        $this->assertEquals($result->getData()->fields, $data);
        $this->assertEquals($result->getItems()->first()->getName(), $childName);
    }
}
