<?php

namespace App\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ItemDTO
{
    private int $id;

    private ItemDataDTO $data;

    private string $name;

    /**
     * @var Collection|ItemDTO[]
     */
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getData(): ?ItemDataDTO
    {
        return $this->data;
    }

    public function setData(ItemDataDTO $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|ItemDTO[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(ItemDTO $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
        }

        return $this;
    }

    public function removeItem(ItemDTO $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
