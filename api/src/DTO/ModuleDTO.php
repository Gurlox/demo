<?php

namespace App\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ModuleDTO
{
    private string $type;

    private string $name;

    private ?int $position;

    private bool $showInMenu;

    private ?string $labelInMenu;

    private ?string $slug;

    /**
     * @var Collection|ItemDTO[]
     */
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function isShowInMenu(): ?bool
    {
        return $this->showInMenu;
    }

    public function setShowInMenu(bool $showInMenu): self
    {
        $this->showInMenu = $showInMenu;

        return $this;
    }

    public function getLabelInMenu(): ?string
    {
        return $this->labelInMenu;
    }

    public function setLabelInMenu(?string $labelInMenu): self
    {
        $this->labelInMenu = $labelInMenu;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
