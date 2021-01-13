<?php

namespace App\Tests\Unit\Service;

use App\DTO\ItemDataDTO;
use App\DTO\ItemDTO;
use App\Entity\Item;
use App\Entity\Module;
use App\Exception\ItemDataNotFoundException;
use App\Exception\ItemNotFoundException;
use App\Service\ItemService;
use App\Tests\Unit\ReflectionUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ItemServiceTest extends TestCase
{
    const PARENT = ['name' => 'parent', 'data' => ['value' => 'parent_data']];
    const CHILD_SUB_ELEMENTS = ['name' => 'subElements', 'data' => ['value' => 'sub_elements_data']];
    const NORMAL_CHILD_OF_SUB_ELEMENTS = ['name' => 'child', 'data' => ['value' => 'child_data']];
    const NORMAL_CHILD = ['name' => 'child', 'data' => ['value' => 'child_data']];

    /**
     * @var EntityManagerInterface|MockObject
     */
    private MockObject $em;

    private ItemService $itemService;

    public function __construct()
    {
        parent::__construct();
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->itemService = new ItemService($this->em);
    }

    public function testGetItemsList(): void
    {
        ['parent' => $parent] = $this->getItemsData();

        $items = new ArrayCollection();
        $items->add($parent);

        $result = $this->itemService->getItemsList($items, true);

        $this->assertEquals($result[self::PARENT['name']]['value'], self::PARENT['data']['value']);
        $this->assertEquals(
            $result[self::PARENT['name']]['items'][self::NORMAL_CHILD['name']]['value'],
            self::NORMAL_CHILD['data']['value']
        );
        $this->assertEquals(
            $result
                [self::PARENT['name']]
                ['items']
                [self::CHILD_SUB_ELEMENTS['name']]
                ['items']
                [self::NORMAL_CHILD_OF_SUB_ELEMENTS['name']]
                ['value'],
            self::NORMAL_CHILD_OF_SUB_ELEMENTS['data']['value']);
    }

    public function testGetItemData(): void
    {
        ['childSubElements' => $childSubElements] = $this->getItemsData();

        $result = $this->itemService->getItemData($childSubElements);

        $this->assertEquals($result['value'], self::CHILD_SUB_ELEMENTS['data']['value']);
        $this->assertEquals(
            $result['items'][self::NORMAL_CHILD_OF_SUB_ELEMENTS['name']]['value'],
            self::NORMAL_CHILD_OF_SUB_ELEMENTS['data']['value']
        );
    }

    public function testUpdateItemsFromDTO(): void
    {
        //dto
        $childDataDTO = new ItemDataDTO();
        $childDataDTO->fields = ['childData' => 'value'];
        $childDTO = (new ItemDTO())->setData($childDataDTO)->setId(2);

        $itemDataDTO = new ItemDataDTO();
        $itemDataDTO->fields = ['data' => 'value'];
        $itemDTO = (new ItemDTO())->setData($itemDataDTO)->setId(1);
        $itemDTO->addItem($childDTO);

        $itemsDTO = new ArrayCollection();
        $itemsDTO->add($itemDTO);

        //items
        $childItem = new Item('name', $this->createMock(Module::class), ['test' => 'value']);
        ReflectionUtil::setProperty($childItem, 'id', 2);

        $children = new ArrayCollection();
        $children->add($childItem);

        $item = new Item('name', $this->createMock(Module::class), ['test2' => 'value']);
        ReflectionUtil::setProperty($item, 'id', 1);
        $item->addChild($childItem);

        $items = new ArrayCollection();
        $items->add($item);

        $this->itemService->updateItemsFromDTO($itemsDTO, $items);

        $this->assertEquals((array) $childDTO->getData()->fields, $childItem->getData());
        $this->assertEquals((array) $itemDTO->getData()->fields, $item->getData());
    }

    public function testUpdateItemsFromDTOExpectingItemNotFoundException(): void
    {
        //dto
        $itemDTO = (new ItemDTO())->setId(2);
        $itemsDTO = new ArrayCollection();
        $itemsDTO->add($itemDTO);

        //items
        $item = new Item('name', $this->createMock(Module::class), ['test2' => 'value']);
        ReflectionUtil::setProperty($item, 'id', 1);
        $items = new ArrayCollection();
        $items->add($item);

        $this->expectException(ItemNotFoundException::class);
        $this->itemService->updateItemsFromDTO($itemsDTO, $items);
    }

    public function testUpdateItemsFromDTOExpectingItemDataNotFoundException(): void
    {
        //dto
        $itemDTO = (new ItemDTO())->setId(1);

        $itemsDTO = new ArrayCollection();
        $itemsDTO->add($itemDTO);

        //items
        $item = new Item('name', $this->createMock(Module::class), ['test2' => 'value']);
        ReflectionUtil::setProperty($item, 'id', 1);

        $items = new ArrayCollection();
        $items->add($item);

        $this->expectException(ItemDataNotFoundException::class);
        $this->itemService->updateItemsFromDTO($itemsDTO, $items);
    }

    /**
     * @return Item[]
     */
    private function getItemsData(): array
    {
        $module = $this->createMock(Module::class);

        $normalChildOfSubElements = new Item(
            self::NORMAL_CHILD_OF_SUB_ELEMENTS['name'],
            $module,
            self::NORMAL_CHILD_OF_SUB_ELEMENTS['data']
        );
        $childSubElements = (new Item(self::CHILD_SUB_ELEMENTS['name'], $module, self::CHILD_SUB_ELEMENTS['data']))
            ->addChild($normalChildOfSubElements)
        ;
        $normalChild = new Item(self::NORMAL_CHILD['name'], $module, self::NORMAL_CHILD['data']);
        $parent = (new Item(self::PARENT['name'], $module, self::PARENT['data']))
            ->addChild($normalChild)
            ->addChild($childSubElements)
        ;

        return [
            'parent' => $parent,
            'normalChild' => $normalChild,
            'normalChildOfSubElements' => $normalChildOfSubElements,
            'childSubElements' => $childSubElements,
        ];
    }
}
