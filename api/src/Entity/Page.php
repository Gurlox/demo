<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PageRepository")
 */
class Page
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="pages")
     * @ORM\JoinColumn(nullable=false)
     */
    private Project $project;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isDefault = true;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Module", mappedBy="page", orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private Collection $modules;

    public function __construct(Project $project)
    {
        $this->modules = new ArrayCollection();
        $this->project = $project;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * @return Collection|Module[]
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
            $module->setPage($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->contains($module)) {
            $this->modules->removeElement($module);
            // set the owning side to null (unless already changed)
            if ($module->getPage() === $this) {
                $module->setPage(null);
            }
        }

        return $this;
    }
}
