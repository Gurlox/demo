<?php

namespace App\Factory;

use App\DTO\ItemDTO;
use App\Entity\Item;
use App\Entity\Module;

class ItemFactory
{
    public function createFromDTO(ItemDTO $itemDTO, Module $module, ?Item $parent = null): Item
    {
        $item = new Item($itemDTO->getName(), $module, $itemDTO->getData()->fields);
        foreach ($itemDTO->getItems() as $child) {
            $item->addChild($this->createFromDTO($child, $module, $item));
        }

        return $item;
    }
}
