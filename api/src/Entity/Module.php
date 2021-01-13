<?php

namespace App\Entity;

use App\Exception\MoveModuleInvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ModuleRepository")
 */
class Module
{
    const TYPE_BOXES = 'boxes';
    const TYPE_GALLERY = 'gallery';
    const TYPE_HEADER = 'header';
    const TYPE_SLIDER = 'slider';
    const TYPE_TEXT = 'text';
    const TYPE_BANNER = 'banner';
    const TYPE_FORM = 'form';
    const TYPE_MAP = 'map';
    const TYPE_FOOTER = 'footer';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $labelInMenu;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $type;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $position;

    /**
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="App\Entity\Page", inversedBy="modules")
     * @ORM\JoinColumn(nullable=false)
     */
    private Page $page;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $showInMenu = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Item", mappedBy="module", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private Collection $items;

    public function __construct(string $name, string $type, Page $page)
    {
        $this->name = $name;
        $this->page = $page;
        $this->setType($type);
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function setType(string $type): self
    {
        if (!in_array($type, [
            self::TYPE_BANNER,
            self::TYPE_BOXES,
            self::TYPE_FOOTER,
            self::TYPE_FORM,
            self::TYPE_HEADER,
            self::TYPE_MAP,
            self::TYPE_SLIDER,
            self::TYPE_TEXT,
            self::TYPE_GALLERY,
        ])) {
            throw new \InvalidArgumentException();
        }
        $this->type = $type;

        return $this;
    }

    /**
     * @throws MoveModuleInvalidArgumentException
     */
    public function moveUp(): self
    {
        if ($this->position === 0) {
            throw new MoveModuleInvalidArgumentException();
        }

        $this->position -= 1;

        return $this;
    }

    /**
     * @throws MoveModuleInvalidArgumentException
     */
    public function moveDown(): self
    {
        if ($this->position === $this->getPage()->getModules()->count() - 1) {
            throw new MoveModuleInvalidArgumentException();
        }
        $this->position += 1;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getShowInMenu(): ?bool
    {
        return $this->showInMenu;
    }

    public function setShowInMenu(bool $showInMenu): self
    {
        $this->showInMenu = $showInMenu;

        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems(bool $onlyParents = true): Collection
    {
        return $onlyParents ? $this->items->filter(function(Item $item) {
            return is_null($item->getParent());
        }) : $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setModule($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getModule() === $this) {
                $item->setModule(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getLabelInMenu(): ?string
    {
        return $this->labelInMenu;
    }

    public function setLabelInMenu(string $labelInMenu): self
    {
        $this->labelInMenu = $labelInMenu;

        return $this;
    }
}
