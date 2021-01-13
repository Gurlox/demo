<?php

namespace App\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ModulesCollectionDTO
{
    /**
     * @var Collection|ModuleDTO[]
     */
    public Collection $modules;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
    }
}
