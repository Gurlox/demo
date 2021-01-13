<?php

namespace App\Service;

use App\DTO\ItemDTO;
use App\Entity\Item;
use App\Exception\ItemDataNotFoundException;
use App\Exception\ItemNotFoundException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class ItemService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function edit(array $payload, Item $item): Item
    {
        $item->setData($payload);
        $this->em->persist($item);
        $this->em->flush();

        return $item;
    }

    /**
     * @param Collection|ItemDTO[] $itemsDTO
     * @param Collection|Item[] $items
     * @throws ItemNotFoundException
     * @throws ItemDataNotFoundException
     */
    public function updateItemsFromDTO(Collection $itemsDTO, Collection $items): void
    {
        foreach ($itemsDTO as $itemDTO) {
            /** @var Item $item */
            $item = $items->filter(function (Item $item) use ($itemDTO) {
                return $item->getId() === $itemDTO->getId();
            })->first();

            if (!$item) {
                throw new ItemNotFoundException($itemDTO->getId());
            }
            if (!$itemDTO->getData()) {
                throw new ItemDataNotFoundException($itemDTO->getId());
            }

            $item->setData((array) $itemDTO->getData()->fields);

            if (!$itemDTO->getItems()->isEmpty()) {
                $this->updateItemsFromDTO($itemDTO->getItems(), $item->getChildren());
            }
        }
    }

    /**
     * @param Collection|Item[] $items
     */
    public function getItemsList(Collection $items, bool $associativeData = false): array
    {
        $itemsData = [];
        foreach ($items as $item) {
            $itemData = $this->getItemData($item, $associativeData);

            if ($associativeData) {
                $itemsData[$item->getName()] = $itemData;
            } else {
                $itemsData[] = $itemData;
            }
        }

        return $itemsData;
    }

    public function getItemData(Item $item, bool $associativeData = false): array
    {
        $itemData = [
            'id' => $item->getId(),
            'items' => $this->getItemsList($item->getChildren(), $item->getName() !== 'elementsList'),
        ];

        if (!$associativeData) {
            $itemData['name'] = $item->getName();
        }

        foreach ($item->getData() as $key => $datum) {
            $itemData[$key] = $datum;
        }

        return $itemData;
    }
}
