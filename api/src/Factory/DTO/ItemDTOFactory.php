<?php

namespace App\Factory\DTO;

use App\DTO\ItemDataDTO;
use App\DTO\ItemDTO;
use App\Entity\Item;

class ItemDTOFactory
{
    public function createFromItem(Item $item): ItemDTO
    {
        $itemDataDTO = new ItemDataDTO();
        $itemDataDTO->fields = $item->getData();
        $itemDTO = new ItemDTO();
        $itemDTO
            ->setName($item->getName())
            ->setData($itemDataDTO)
        ;

        foreach ($item->getChildren() as $child) {
            $itemDTO->addItem($this->createFromItem($child));
        }

        return $itemDTO;
    }
}
